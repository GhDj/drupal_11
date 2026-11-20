<?php

declare(strict_types=1);

namespace Drupal\csn_custom\Plugin\Field\FieldWidget;

use Drupal\Core\Entity\Element\EntityAutocomplete;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\Attribute\FieldWidget;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\OptionsButtonsWidget;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\taxonomy\TermInterface;

/**
 * Defines the 'csn_taxonomy_subtree' field widget.
 */
#[FieldWidget(
  id: 'csn_taxonomy_subtree',
  label: new TranslatableMarkup('Taxonomy Subtree'),
  field_types: [
    'entity_reference',
  ],
  multiple_values: TRUE,
)]
final class CsnTaxonomySubtreeWidget extends OptionsButtonsWidget implements ContainerFactoryPluginInterface {

  /**
   * The array of options for the widget, filtered by parent.
   */
  protected array $subtreeOptions = [];

  /**
   * Constructs the plugin instance.
   */
  public function __construct(
    string $plugin_id,
    array $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    array $third_party_settings,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return [
      'parent' => NULL,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $element['parent'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('Parent'),
      '#description' => $this->t('Restrict offered terms to direct children of this term.'),
      '#target_type' => 'taxonomy_term',
      '#target_bundles' => $this->getFieldSetting('handler_settings')['target_bundles'],
      '#element_validate' => [
        [EntityAutocomplete::class, 'validateEntityAutocomplete'],
        [static::class, 'validateParent'],
      ],
      '#default_value' => $this->getParentEntity(),
    ];
    return $element;
  }

  /**
   * Callback to validate and transform the selected entity to uuid.
   */
  public static function validateParent(array $element, FormStateInterface $form_state): void {
    if ($target_id = $form_state->getValue($element['#parents'])) {
      $entity = \Drupal::entityTypeManager()->getStorage('taxonomy_term')
        ->load($target_id);
      $form_state->setValueForElement($element, $entity?->uuid());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    $entity = $this->getParentEntity();
    return [
      $entity
        ? $this->t('Children of %parent', ['%parent' => $entity->label()])
        : $this->t('Top level'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);
    if (!$this->subtreeOptions) {
      $entity = $this->getParentEntity();
      $childTerms = $this->entityTypeManager->getStorage('taxonomy_term')
        ->loadByProperties([
          'vid' => $this->getFieldSetting('handler_settings')['target_bundles'],
          'parent' => $entity?->id() ?? 0,
        ]);

      // Parent already checked for options, no need to check access again.
      $options = [];
      foreach ($this->options as $key => $label) {
        if (!is_array($label) && isset($childTerms[$key])) {
          $options[$key] = $childTerms[$key]?->label();
        }
      }
      array_walk_recursive($options, [$this, 'sanitizeLabel']);

      $this->subtreeOptions = $options;
    }

    $element['#options'] = $this->subtreeOptions;

    return $element;
  }

  /**
   * Helper to get the parent entity, if configured.
   */
  protected function getParentEntity(): ?TermInterface {
    $entity = NULL;
    if ($uuid = $this->getSetting('parent')) {
      $entity = $this->entityTypeManager->getStorage('taxonomy_term')
        ->loadByProperties(['uuid' => $uuid]);
      $entity = reset($entity) ?: NULL;
    }
    return $entity;
  }

}
