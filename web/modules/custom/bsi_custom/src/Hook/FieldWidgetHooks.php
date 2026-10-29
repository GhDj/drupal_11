<?php

declare(strict_types=1);

namespace Drupal\bsi_custom\Hook;

use Drupal\content_moderation\ModerationInformationInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Render\Element;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\link\LinkItemInterface;

/**
 * Theme hooks related to the field widgets.
 */
class FieldWidgetHooks {

  use StringTranslationTrait;

  public function __construct(
    protected readonly EntityTypeManagerInterface $entityTypeManager,
    protected readonly ModerationInformationInterface $moderationInformation,
  ) {}

  /**
   * Implements hook_field_widget_single_element_WIDGET_TYPE_form_alter() for text_textarea_with_summary.
   */
  #[Hook('field_widget_single_element_text_textarea_with_summary_form_alter')]
  public function alterSummaryWidget(array &$element, FormStateInterface $form_state, array $context): void {
    /** @var \Drupal\Core\Field\FieldItemListInterface $items */
    $items = $context['items'];

    $element['summary']['#title'] = $this->t('@label (@summary)', [
      '@label' => $element['#title'],
      '@summary' => $element['summary']['#title'],
    ]);

    // The default description implies that an empty summary = output full text,
    // but we use TextSummaryFormatter to explicitly render the summary.
    $description = [];
    $description[] = $this->t('Displayed on certain teaser view modes in place of the full @label field.', [
      '@label' => $element['#title'],
    ]);
    if (
      !$items->getFieldDefinition()->getSetting('required_summary')
      && $items->getEntity()->bundle() !== 'tile_item'
    ) {
      $description[] = $this->t('Leave blank to display no text.');
    }
    $element['summary']['#description'] = array_map(
      static fn ($text) => ['#markup' => $text, '#suffix' => ' '],
      $description,
    );
  }

  /**
   * Implements hook_field_widget_single_element_WIDGET_TYPE_form_alter() for link_default.
   */
  #[Hook('field_widget_single_element_link_default_form_alter')]
  public function alterLinkWidget(array &$element, FormStateInterface $form_state, array $context): void {
    /** @var \Drupal\Core\Field\FieldItemListInterface $items */
    $items = $context['items'];

    if ($items->getName() === 'field_downloads') {
      // LinkWidget only supports nodes, but we try to patch around that.
      // @see \Drupal\link\Plugin\Field\FieldWidget\LinkWidget::getUserEnteredStringAsUri
      $element['uri']['#target_type'] = 'media';
      $element['uri']['#selection_settings'] = [
        'target_bundles' => ['publication', 'download'],
      ];
    }

    if ($items->getSetting('link_type') === LinkItemInterface::LINK_INTERNAL) {
      $target_type = $this->entityTypeManager->getDefinition($element['uri']['#target_type'] ?? 'node');
      $element['uri']['#title'] = $target_type->getLabel();
      $element['uri']['#size'] = 32;
    }
  }

  /**
   * Implements hook_field_widget_complete_WIDGET_TYPE_form_alter() for link_default.
   */
  #[Hook('field_widget_complete_link_default_form_alter')]
  public function alterLinkCompleteWidget(array &$elements, FormStateInterface $form_state, $context): void {
    // Don't repeat uri help text.
    $elements['#type'] = 'fieldgroup';
    $elements['#description'] = $elements['widget'][0]['uri']['#description'];
    foreach (Element::children($elements['widget']) as $key) {
      unset($elements['widget'][$key]['uri']['#description']);
    }
  }

  /**
   * Implements hook_field_widget_complete_WIDGET_TYPE_form_alter() for boolean_checkbox.
   */
  #[Hook('field_widget_complete_boolean_checkbox_form_alter')]
  public function alterCheckboxCompleteWidget(array &$elements, FormStateInterface $form_state, $context): void {
    /** @var \Drupal\Core\Field\WidgetInterface $widget */
    $widget = $context['widget'];
    if ($widget->getSetting('display_label') === FALSE) {
      $elements['widget']['#type'] = 'item';
      $elements['widget']['#input'] = FALSE;
    }
  }

  /**
   * Implements hook_field_widget_single_element_WIDGET_TYPE_form_alter() for moderation_state_default.
   *
   * After a preview round-trip, the "Current state" label may reflect the
   * previewed state rather than the actual saved state. This corrects it by
   * always loading the persisted moderation state from the database.
   */
  #[Hook('field_widget_single_element_moderation_state_default_form_alter')]
  public function alterModerationStateWidget(array &$element, FormStateInterface $form_state, array $context): void {
    /** @var \Drupal\Core\Entity\ContentEntityInterface $entity */
    $entity = $context['items']->getEntity();

    if ($entity->isNew() || !$entity->getEntityType()->isRevisionable()) {
      return;
    }

    $storage = $this->entityTypeManager->getStorage($entity->getEntityTypeId());
    $original = $storage->loadRevision($entity->getLoadedRevisionId());
    if ($original === NULL) {
      return;
    }

    if (!$entity->isDefaultTranslation() && $original->hasTranslation($entity->language()->getId())) {
      $original = $original->getTranslation($entity->language()->getId());
    }

    $workflow = $this->moderationInformation->getWorkflowForEntity($original);
    if ($workflow === NULL) {
      return;
    }

    $saved_state_id = $original->get('moderation_state')->value;
    $workflow_type = $workflow->getTypePlugin();
    if ($workflow_type->hasState($saved_state_id)) {
      $element['current']['#markup'] = $workflow_type->getState($saved_state_id)->label();
    }
  }

  /**
   * Implements hook_geocoder_address_values_alter().
   */
  #[Hook('geocoder_address_values_alter')]
  public function alterGeocoderAddress(array &$values): void {
    if ($values['country_code'] === 'de' && empty($values['postal_code'])) {
      $values['country_code'] = '';
    }
  }

}
