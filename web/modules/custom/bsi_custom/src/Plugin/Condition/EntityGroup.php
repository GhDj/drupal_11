<?php

declare(strict_types=1);

namespace Drupal\bsi_custom\Plugin\Condition;

use Drupal\bsi_custom\Plugin\Derivative\EntityGroupConditionDeriver;
use Drupal\Core\Condition\Attribute\Condition;
use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\Context\EntityContextDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\group\Entity\GroupInterface;
use Drupal\group\Plugin\Group\Relation\GroupRelationTypeInterface;
use Drupal\group\Plugin\Group\Relation\GroupRelationTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Condition plugin evaluating an entity's group relationships.
 */
#[Condition(
  id: 'entity_group_relation',
  label: new TranslatableMarkup('Group relation'),
  context_definitions: [
    'entity' => new EntityContextDefinition(),
  ],
  deriver: EntityGroupConditionDeriver::class,
)]
class EntityGroup extends ConditionPluginBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    string $plugin_id,
    array $plugin_definition,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly GroupRelationTypeManagerInterface $groupRelationTypeManager,
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
      $container->get('group_relation_type.manager'),
    );
  }

  /**
   * {@inheritDoc}
   */
  public function defaultConfiguration(): array {
    return [
      'groups' => [],
      'types' => [],
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritDoc}
   */
  public function summary(): TranslatableMarkup|string {
    $groups = $this->entityTypeManager->getStorage('group')
      ->loadMultiple($this->configuration['groups']);

    $types = [];
    foreach ($this->configuration['types'] as $type) {
      $types[$type] = $this->groupRelationTypeManager->getDefinition($type);
    }

    if ($groups && $types) {
      return $this->t('Relation of type %types within %groups', [
        '%types' => implode(', ', array_map(static fn (GroupRelationTypeInterface $type) => $type->getLabel(), $types)),
        '%groups' => implode(', ', array_map(static fn (GroupInterface $group) => $group->label(), $groups)),
      ]);
    }
    elseif ($groups) {
      return $this->t('Relation to %groups', [
        '%groups' => implode(', ', array_map(static fn (GroupInterface $group) => $group->label(), $groups)),
      ]);
    }
    else {
      return $this->t('Relation of type %types', [
        '%types' => implode(', ', array_map(static fn (GroupRelationTypeInterface $type) => $type->getLabel(), $types)),
      ]);
    }
  }

  /**
   * {@inheritDoc}
   */
  public function evaluate(): bool {
    /** @var \Drupal\Core\Entity\EntityInterface $entity */
    $entity = $this->getContext('entity')->getContextValue();

    $storage = $this->entityTypeManager->getStorage('group_relationship');
    $relationships = $storage->loadByEntity($entity);

    foreach ($relationships as $relationship) {
      $group_ok = FALSE;
      $type_ok = FALSE;

      /** @var \Drupal\group\Entity\GroupRelationshipInterface $relationship */
      if (empty($this->configuration['groups']) || in_array($relationship->getGroupId(), $this->configuration['groups'])) {
        $group_ok = TRUE;
      }
      if (empty($this->configuration['types']) || in_array($relationship->getPluginId(), $this->configuration['types'])) {
        $type_ok = TRUE;
      }
      if ($group_ok && $type_ok) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * {@inheritDoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {
    $entity_type_id = explode(':', $this->getContextDefinition('entity')->getDataType())[1];

    // @todo Can we go with entity ids, or do we need uuids/something else?
    $form['groups'] = [
      '#type' => 'checkboxes',
      '#title' => new TranslatableMarkup('Group'),
      '#options' => array_map(
        static fn (GroupInterface $group) => $group->label(),
        $this->entityTypeManager->getStorage('group')->loadMultiple(),
      ),
      '#element_validate' => [[static::class, 'validateCheckboxes']],
      '#default_value' => $this->configuration['groups'],
    ];

    $form['types'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Group relation types'),
      '#options' => array_map(
        static fn (GroupRelationTypeInterface $type) => $type->getLabel(),
        array_filter($this->groupRelationTypeManager->getDefinitions(),
          static fn (GroupRelationTypeInterface $type) => $type->getEntityTypeId() === $entity_type_id),
      ),
      '#element_validate' => [[static::class, 'validateCheckboxes']],
      '#default_value' => $this->configuration['types'],
    ];

    return parent::buildConfigurationForm($form, $form_state);
  }

  /**
   * Validation callback to remove unchecked options from stored data.
   */
  public static function validateCheckboxes(array &$element, FormStateInterface $form_state, array &$complete_form): void {
    $value = $form_state->getValue($element['#parents']);
    $form_state->setValue($element['#parents'], array_values(array_filter($value)));
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state): void {
    $this->configuration['groups'] = $form_state->getValue('groups');
    $this->configuration['types'] = $form_state->getValue('types');

    parent::submitConfigurationForm($form, $form_state);
  }

}
