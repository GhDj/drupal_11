<?php

declare(strict_types=1);

namespace Drupal\bsi_custom\Plugin\Condition;

use Drupal\bsi_custom\Plugin\Derivative\EntityFieldConditionDeriver;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\Component\Uuid\Uuid;
use Drupal\Core\Condition\Attribute\Condition;
use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormElementHelper;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\Context\EntityContextDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinitionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Condition plugin evaluating an entity' field values.
 */
#[Condition(
  id: 'entity_field',
  label: new TranslatableMarkup('Field value'),
  context_definitions: [
    'entity' => new EntityContextDefinition(),
  ],
  deriver: EntityFieldConditionDeriver::class,
)]
class EntityField extends ConditionPluginBase implements ContainerFactoryPluginInterface {

  const string OPERATOR_NULL = 'null';

  const string OPERATOR_EQUALS = 'equals';

  const string OPERATOR_TEXT_CONTAINS = 'contains';

  public function __construct(
    array $configuration,
    string $plugin_id,
    array $plugin_definition,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly EntityTypeBundleInfoInterface $entityTypeBundleInfo,
    private readonly EntityFieldManagerInterface $entityFieldManager,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('entity_type.bundle.info'),
      $container->get('entity_field.manager'),
    );
  }

  /**
   * {@inheritDoc}
   */
  public function defaultConfiguration(): array {
    return [
      'entity_bundle' => [],
      'field_name' => NULL,
      'field_property' => [],
      'operator' => self::OPERATOR_EQUALS,
      'value' => '',
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritDoc}
   */
  public function summary(): TranslatableMarkup|string {
    $value = $this->configuration['operator'] === static::OPERATOR_NULL
      ? ''
      : $this->configuration['value'];

    $field = $this->entityFieldManager->getFieldStorageDefinitions($this->getEntityTypeId())[$this->configuration['field_name']];
    if (Uuid::isValid($value) && array_intersect_key(
      $field->getPropertyDefinitions(),
      array_flip($this->configuration['field_property'])
    )) {
      $target_entities = $this->entityTypeManager->getStorage($field->getSetting('target_type'))
        ->loadByProperties(['uuid' => $value]);
      $value = !$target_entities ? $value : new FormattableMarkup('%label', [
        '%label' => reset($target_entities)?->toLink()->toString(),
      ]);
    }

    return $this->t('@entity_type field %field (@property) @operator "@value"', [
      '@entity_type' => $this->entityTypeManager->getDefinition($this->getEntityTypeId())->getLabel(),
      '%field' => $this->configuration['field_name'],
      '@property' => implode(', ', $this->configuration['field_property']),
      '@operator' => match ($this->configuration['operator']) {
        static::OPERATOR_EQUALS => $this->isNegated()
          ? $this->t('is not equal to')
          : $this->t('equals'),
        static::OPERATOR_TEXT_CONTAINS => $this->isNegated()
          ? $this->t('does not contain')
          : $this->t('contains'),
        static::OPERATOR_NULL => $this->isNegated()
          ? $this->t('is not empty')
          : $this->t('is empty'),
      },
      '@value' => $value,
    ]);
  }

  /**
   * {@inheritDoc}
   */
  public function evaluate(): bool {
    /** @var \Drupal\Core\Entity\FieldableEntityInterface $entity */
    $entity = $this->getContext('entity')->getContextValue();

    $field_name = $this->configuration['field_name'];
    $bundles = $this->configuration['entity_bundle'];
    $operator = $this->configuration['operator'];
    if (!$entity->hasField($field_name)
      || ($bundles && !in_array($entity->bundle(), $bundles))
    ) {
      return FALSE;
    }

    if ($operator === self::OPERATOR_NULL) {
      return $entity->get($field_name)->isEmpty();
    }

    $field = $this->entityFieldManager
      ->getFieldStorageDefinitions($entity->getEntityTypeId())[$field_name];

    $property_names = $this->configuration['field_property'];
    $value = $this->configuration['value'];

    $result = FALSE;
    foreach ($entity->get($field_name) as $item) {
      foreach ($property_names as $property_name) {
        if ($field->getPropertyDefinition($property_name)->getDataType() === 'entity_reference') {
          /** @var \Drupal\Core\Entity\EntityInterface $referenced_entity */
          $referenced_entity = $item->{$property_name};
          $result = $result || $this->compare($referenced_entity->label(), $operator, $value);
          $result = $result || $this->compare($referenced_entity->uuid(), $operator, $value);
        }
        else {
          $result = $result || $this->compare($item->{$property_name}, $operator, $value);
        }
        if ($result === TRUE) {
          return TRUE;
        }
      }
    }

    return FALSE;
  }

  /**
   * Operator comparison between field value and configured value.
   */
  protected function compare(mixed $propertyValue, mixed $operator, string $comparisonValue): bool {
    return match ($operator) {
      static::OPERATOR_EQUALS => (string) $propertyValue === $comparisonValue,
      static::OPERATOR_TEXT_CONTAINS => str_contains((string) $propertyValue, $comparisonValue),
    };
  }

  /**
   * Helper method to get the plugin's entity type id.
   */
  protected function getEntityTypeId(): string {
    /** @var \Drupal\Core\Plugin\Context\EntityContextDefinition $context_definition */
    $context_definition = $this->getContextDefinition('entity');
    return str_replace('entity:', '', $context_definition->getDataType());
  }

  /**
   * {@inheritDoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {
    $entity_type_id = $this->getEntityTypeId();

    $form['entity_type'] = [
      '#type' => 'value',
      '#value' => $entity_type_id,
    ];

    $form['entity_bundle'] = [
      '#type' => 'checkboxes',
      '#title' => $this->entityTypeManager->getDefinition($entity_type_id)->getBundleLabel(),
      '#options' => $this->entityTypeBundleInfo->getBundleLabels($entity_type_id),
      '#element_validate' => [[static::class, 'validateCheckboxes']],
      '#default_value' => $this->configuration['entity_bundle'],
    ];

    $form['field_name'] = [
      '#type' => 'select',
      '#title' => $this->t('Field'),
      '#validated' => TRUE,
      '#options' => array_map(static fn (FieldStorageDefinitionInterface $field) => $field->getLabel(), $this->entityFieldManager->getFieldStorageDefinitions($entity_type_id)),
      '#empty_option' => $this->t('- None -'),
      '#default_value' => $this->configuration['field_name'],
      '#ajax' => [
        'callback' => [$this, 'onFieldChanged'],
        'wrapper' => strtr($this->getPluginId(), ':', '-') . '-field-property',
        'event' => 'change',
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Loading field properties...'),
        ],
      ],
      // @fixme There may still be duplicate uses of the plugin on the page.
      '#plugin_id' => $this->getPluginId(),
    ];

    $field_name = $form_state->getValue('field_name')
      ?? $this->configuration['field_name']
      ?? '';
    if ($form_state->isRebuilding() && ($trigger = $form_state->getTriggeringElement())
      && $trigger['#plugin_id'] === $this->getPluginId()
    ) {
      $field_name = $form_state->getValue($trigger['#parents']);
    }

    $properties = [];
    if ($field_name) {
      $properties = $this->entityFieldManager
        ->getFieldStorageDefinitions($entity_type_id)[$field_name]
        ->getPropertyDefinitions();
    }
    $form['field_property'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Property'),
      '#prefix' => '<div id="' . $form['field_name']['#ajax']['wrapper'] . '">',
      '#suffix' => '</div>',
      '#options' => array_map(static fn (DataDefinitionInterface $property)
        => new TranslatableMarkup('@label (@type)', [
          '@label' => $property->getLabel(),
          '@type' => $property->getDataType(),
        ]), $properties),
      '#description' => empty($properties) ? $this->t('Select a field to view available properties.') : NULL,
      '#default_value' => $this->configuration['field_property'],
      '#element_validate' => [[static::class, 'validateCheckboxes']],
      '#required' => !empty($properties),
    ];
    foreach ($properties as $property => $definition) {
      $form['field_property'][$property]['#description'] = match ($definition->getDataType()) {
        'boolean' => $this->t('This property compares against "0" and "1".'),
        'entity_reference' => $this->t('This property compares against the target entity label and uuid.'),
        default => $definition->getDescription(),
      };
    }

    $form['operator'] = [
      '#type' => 'select',
      '#title' => $this->t('Operator'),
      '#options' => [
        self::OPERATOR_NULL => $this->t('Is NULL'),
        self::OPERATOR_EQUALS => $this->t('Equals'),
        self::OPERATOR_TEXT_CONTAINS => $this->t('Contains'),
      ],
      '#default_value' => $this->configuration['operator'],
      '#attributes' => [
        'class' => ['js-value-operator'],
      ],
    ];

    $form['value'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Value'),
      '#default_value' => $this->configuration['value'],
      '#states' => [
        'visible' => [
          ':input.js-value-operator' => ['!value' => static::OPERATOR_NULL],
        ],
      ],
    ];

    return parent::buildConfigurationForm($form, $form_state);
  }

  /**
   * Validation callback to remove unchecked options from stored data.
   */
  public static function validateCheckboxes(array &$element, FormStateInterface $form_state, array &$complete_form): void {
    $value = $form_state->getValue($element['#parents']);
    $form_state->setValue($element['#parents'], array_filter($value));
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state): void {
    $this->configuration['entity_bundle'] = $form_state->getValue('entity_bundle');
    $this->configuration['field_name'] = $form_state->getValue('field_name');
    $this->configuration['field_property'] = $form_state->getValue('field_property');
    $this->configuration['operator'] = $form_state->getValue('operator');
    $this->configuration['value'] = $form_state->getValue('value');

    parent::submitConfigurationForm($form, $form_state);
  }

  /**
   * Handles switching the selected fields.
   */
  public function onFieldChanged(array &$form, FormStateInterface &$form_state, Request $request): JsonResponse|array {
    $trigger = $form_state->getTriggeringElement();
    $parents = $trigger['#parents'];
    array_pop($parents);

    $sub_form = FormElementHelper::getElementByName(implode('][', $parents), $form) ?: $form;
    return $sub_form['field_property'];
  }

}
