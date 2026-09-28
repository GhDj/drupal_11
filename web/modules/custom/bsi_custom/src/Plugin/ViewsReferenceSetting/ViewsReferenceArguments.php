<?php

namespace Drupal\bsi_custom\Plugin\ViewsReferenceSetting;

use Drupal\Core\Entity\Element\EntityAutocomplete;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\views\Plugin\views\argument\ArgumentPluginBase;
use Drupal\views\ViewExecutable;
use Drupal\viewsreference\Plugin\ViewsReferenceSetting\ViewsReferenceArgument as ContribViewsReferenceArgument;

/**
 * Views reference setting plugin to override context arguments.
 *
 * @ViewsReferenceSetting(
 *   id = "argument_override",
 *   label = @Translation("Arguments"),
 *   default_value = {},
 * )
 */
class ViewsReferenceArguments extends ContribViewsReferenceArgument {

  use StringTranslationTrait;

  const int MAX_OPTIONS_TO_DISPLAY = 10;

  /**
   * {@inheritdoc}
   */
  public function alterFormField(array &$form_field): void {
    /** @var \Drupal\viewsreference_filter\ViewsRefFilterUtilityInterface $utility */
    $utility = \Drupal::service('viewsreference_filter.views_utility');
    $view = $utility->loadView(
      $this->configuration['view_name'],
      $this->configuration['display_id'],
    );
    if (empty($view->argument)) {
      $form_field = [];
      return;
    }

    $form_field['#type'] = 'item';
    $form_field['#weight'] = 40;

    foreach ($view->argument as $plugin) {
      $is_negated = (bool) $plugin->options['not'];
      $exception_value = $plugin->options['exception']['value'] ?? NULL;

      $description = [];
      if ($exception_value) {
        $description[] = $this->t('Specify an override value or use <code>@exception</code> to skip this argument.', [
          '@exception' => $exception_value,
        ]);
      }
      else {
        $description[] = $this->t('Specify an override value for this argument.', [
          '@exception' => $exception_value,
        ]);
      }
      if ($plugin->options['break_phrase']) {
        $description[] = $this->t('Enter multiple values in the form of 1+2+3 (for OR) or 1,2,3 (for AND).');
      }
      $description[] = $this->t('Leave empty to use the default.');

      $form_field[$plugin->options['id']] = [
        '#type' => 'textfield',
        '#title' => $plugin->adminLabel(),
        '#field_suffix' => $is_negated ? $this->t('@label (negated)', [
          '@label' => '',
        ]) : NULL,
        '#size' => NULL,
        '#default_value' => $form_field['#default_value'][$plugin->options['id']] ?? NULL,
        '#description' => array_map(
          static fn ($text) => ['#markup' => $text, '#suffix' => ' '],
          $description,
        ),
      ];

      [$widget, $options] = $this->getWidgetData($plugin);
      if ($widget !== NULL) {
        $this->alterWidget($form_field[$plugin->options['id']], $widget, $options);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function alterView(ViewExecutable $view, mixed $value): void {
    if (empty($value)) {
      return;
    }

    // @todo Need to test if this keeps the order intact.
    $arguments = implode('/', $value);
    parent::alterView($view, $arguments);
  }

  /**
   * Helper to decide on which widget to use.
   *
   * @return list<string, array<string, mixed>>
   *   The widget override data, contains widget type/target type + options.
   */
  protected function getWidgetData(ArgumentPluginBase $plugin): array {
    $widget = NULL;
    $options = [];
    if ($plugin->options['validate'] && str_starts_with($plugin->options['validate']['type'], 'entity:')) {
      [, $entity_type_id] = explode(':', $plugin->options['validate']['type']);
      $storage = \Drupal::entityTypeManager()->getStorage($entity_type_id);

      $count = $storage->getQuery()->accessCheck(TRUE)->count();
      $bundles = $plugin->options['validate_options']['bundles'] ?? [];
      if ($bundles) {
        $count->condition($storage->getEntityType()
          ->getKey('bundle'), $bundles, 'IN');
      }
      if ($count->execute() <= self::MAX_OPTIONS_TO_DISPLAY) {
        $widget = ($plugin->options['break_phrase'] ?? FALSE) ? 'checkboxes' : 'select';
        $options = array_map(
          static fn(EntityInterface $entity) => $entity->label(),
          $storage->loadByProperties([
            $storage->getEntityType()->getKey('bundle') => $bundles,
          ])
        );
        $options[$plugin->options['exception']['value']] = $this->t('- Any -');
      }
      else {
        $widget = $entity_type_id;
        $options = $bundles;
      }
    }
    if (empty($options) && $plugin->getPluginId() === 'search_api') {
      // @todo Use dependency injection.
      /** @var \Drupal\search_api\IndexInterface $index */
      $index = \Drupal::entityTypeManager()->getStorage('search_api_index')
        ->load(str_replace('search_api_index_', '', $plugin->table));

      $sapi_field_settings = $index->get('field_settings')[$plugin->options['field']];
      [, $entity_type_id] = explode(':', $sapi_field_settings['datasource_id']);
      [$field_name] = explode(':', $sapi_field_settings['property_path']);

      /** @var \Drupal\Core\Entity\EntityFieldManagerInterface $efm */
      $efm = \Drupal::service('entity_field.manager');
      if ($field_definition = $efm->getFieldStorageDefinitions($entity_type_id)[$field_name] ?? NULL) {
        if ($field_options = $field_definition->getSetting('allowed_values') ?? []) {
          $widget = ($plugin->options['break_phrase'] ?? FALSE) ? 'checkboxes' : 'select';
          $options = $field_options;
        }
      }
    }

    return [$widget, $options];
  }

  /**
   * Change the form widget to something useful.
   */
  public function alterWidget(array &$element, mixed $widget, mixed $options): void {
    if (in_array($widget, ['checkboxes', 'radios', 'select'], TRUE)) {
      $element['#type'] = $widget;
      $element['#options'] = $options;
      $element['#empty_option'] = $this->t('Use default');
      asort($element['#options']);
    }
    elseif ($widget) {
      // Widget is the entity type id, options the allowed bundles.
      $element['#type'] = 'entity_autocomplete';
      $element['#placeholder'] = $this->t('Use default');
      $element['#target_type'] = $widget;
      $element['#selection_settings']['target_bundles'] = $options;
      $element['#multiple'] = (bool) ($plugin->options['break_phrase'] ?? FALSE);
      $element['#element_validate'][] = [
        static::class,
        'validateEntityAutocomplete',
      ];

      if ($element['#default_value']) {
        $entities = \Drupal::entityTypeManager()->getStorage($widget)
          ->loadMultiple((array) $element['#default_value']);
        $element['#default_value'] = $element['#multiple'] ? $entities : reset($entities);
      }
    }
  }

  /**
   * Validation callback handler to transform submitted data for storage.
   */
  public static function validateEntityAutocomplete(array &$element, FormStateInterface $form_state, array &$complete_form): void {
    $values = $form_state->getValue($element['#parents']);

    $new_values = [];
    foreach ((array) $values as $value) {
      $new_values[] = EntityAutocomplete::extractEntityIdFromAutocompleteInput($value);
    }
    $new_value = array_filter($new_values);
    if (empty($element['#multiple'])) {
      $new_value = reset($new_value);
    }
    $form_state->setValueForElement($element, $new_value);
  }

}
