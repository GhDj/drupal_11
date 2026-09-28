<?php

declare(strict_types=1);

namespace Drupal\bsi_custom\Hook;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\DependencyInjection\DependencySerializationTrait;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\FieldConfigInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Render\Element;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\field\Entity\FieldConfig;
use Drupal\language\Config\LanguageConfigFactoryOverrideInterface;

/**
 * Hooks related to the fields and their formatters.
 */
class PresetOptionsHooks {

  use StringTranslationTrait;
  use DependencySerializationTrait;

  public function __construct(
    protected readonly LanguageConfigFactoryOverrideInterface $configFactoryOverride,
    protected readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * Implements hook_form_BASE_FORM_ID_alter() for field_config_edit_form.
   */
  #[Hook('form_field_config_edit_form_alter')]
  public function addFieldThirdPartySettings(array &$form, FormStateInterface $form_state, string $form_id): void {
    /** @var \Drupal\field\Entity\FieldConfig $entity */
    $entity = $form_state->getFormObject()->getEntity();
    if ($this->getPresetProperty($entity) === NULL) {
      return;
    }

    $form['third_party_settings']['bsi_custom'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('BSI Custom'),
      '#description_display' => 'before',
      '#description' => $this->t('Provide predefined options to the form widget of arbitrary field types. This feature is based on "Select Or Other", optionally allowing to provide a custom value.'),
    ];
    $preset_options = $entity->getThirdPartySetting('bsi_custom', 'preset_options', []);
    if (!array_is_list($preset_options)) {
      array_walk($preset_options,
        static fn (&$value, $key) => $value = $key !== $value ? $key . '|' . $value : $value
      );
    }
    $form['third_party_settings']['bsi_custom']['preset_options'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Preset options'),
      '#description' => array_map(
        static fn ($text) => ['#markup' => $text, '#suffix' => ' '],
        [
          $this->t('Enter one item per line.'),
          $this->t('Keys may be provided using the format <code>key|label</code>.'),
          $this->t('Example: @example.', ['@example' => 'foo|Foo']),
        ],
      ),
      '#default_value' => implode("\n",
        array_values($preset_options),
      ),
    ];
    $form['third_party_settings']['bsi_custom']['preset_other'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Other option'),
      '#description' => $this->t('Leave empty to enforce one of the preset options.'),
      '#default_value' => $entity->getThirdPartySetting('bsi_custom', 'preset_other', 'Other…'),
      '#states' => [
        'visible' => [
          ':input[name="third_party_settings[bsi_custom][preset_options]"]' => ['empty' => FALSE],
        ],
      ],
    ];

    $form['#entity_builders'][] = [static::class, 'saveThirdPartySettings'];
  }

  /**
   * Callback handler to save sanitized third party settings.
   */
  public static function saveThirdPartySettings(string $entity_type, ConfigEntityInterface $entity, array &$form, FormStateInterface $form_state): void {
    $value = $form_state->getValue(['third_party_settings', 'bsi_custom', 'preset_options']);
    $options = array_filter(array_map('trim', explode("\n", $value ?? [])));
    if ($options) {
      $config = [];
      foreach ($options as $option) {
        [$key, $value] = explode('|', $option);
        if ($value !== NULL) {
          $config[$key] = $value;
        }
        else {
          $config[] = $key;
        }
      }
      $entity->setThirdPartySetting('bsi_custom', 'preset_options', $config);
    }
    else {
      $entity->unsetThirdPartySetting('bsi_custom', 'preset_options');
    }

    $other = $form_state->getValue(['third_party_settings', 'bsi_custom', 'preset_other']);
    if ($options || $other !== 'Other…') {
      $entity->setThirdPartySetting('bsi_custom', 'preset_other', $other ?: '');
    }
    else {
      $entity->unsetThirdPartySetting('bsi_custom', 'preset_other');
    }
  }

  /**
   * Implements hook_field_widget_single_element_form_alter().
   */
  #[Hook('field_widget_single_element_form_alter')]
  public function alterWidgetFormAddPresets(array &$element, FormStateInterface $form_state, array $context): void {
    $field_config = $context['items']->getFieldDefinition();
    if (!$field_config instanceof FieldConfig
      || !in_array('bsi_custom', $field_config->getThirdPartyProviders(), TRUE)) {
      return;
    }

    $preset_options = $field_config->getThirdPartySetting('bsi_custom', 'preset_options', []);
    $preset_other = $field_config->getThirdPartySetting('bsi_custom', 'preset_other', NULL);

    $property = $this->getPresetProperty($field_config);
    if (!isset($element[$property]) || (count($preset_options) == 0 && strlen($preset_other) === 0)) {
      return;
    }

    $default_value = $element[$property]['#default_value'];

    $preset_is_list = $preset_options && array_is_list($preset_options);
    if ($preset_is_list) {
      /** @var \Drupal\Core\Entity\ContentEntityInterface $entity */
      $entity = $context['items']->getEntity();
      if ($field_config->isTranslatable() && $entity->isNewTranslation() && !$entity->isNew()) {
        // Update default value based on translation source selected value.
        $translated_options = $this->getPresetOptions(
          $field_config,
          $entity->getUntranslated()->language()->getId(),
        );
        if (in_array($default_value, $translated_options)) {
          $default_value = $preset_options[array_search($default_value, $translated_options)];
        }
      }
      if (in_array($default_value, $preset_options, TRUE)) {
        $default_value = array_search($default_value, $preset_options);
      }
    }
    $is_preset_value = array_key_exists($default_value, $preset_options);

    // @see \Drupal\select_or_other\Element\Select
    $element[$property] = [
      '#type' => 'select_or_other_select',
      '#default_value' => $default_value !== NULL ? [$default_value]
        : ($element[$property]['#required'] ? [reset($preset_options)] : NULL),
      '#key_column' => $property,
      '#no_empty_option' => $element[$property]['#required'],
      '#other_option' => $preset_other ?? $this->t('Other…'),
      '#other_field_label' => $this->t('Other value'),
      // '#other_placeholder' => $this->t('Enter other value'),
      '#options' => $preset_options,
      '#other_options' => !$is_preset_value && strlen((string) $default_value) > 0 ? [$default_value] : [],
      '#input_type' => $element[$property]['#type'],
      // '#merged_values' => TRUE,
      '#other_allowed' => $preset_other !== '',
      '#element_validate' => array_merge([
        [$this, 'validateElement'],
      ], $element[$property]['#element_validate'] ?? []),
      '#translated_presets' => $preset_is_list && $field_config->isTranslatable(),
      '#field_config' => $field_config,
    ] + $element[$property];

    // @fixme #element_validate runs too late and causes issues with mb_strlen
    // call in \Drupal\Core\Form\FormValidator::performRequiredValidation.
    unset($element[$property]['#maxlength']);
  }

  /**
   * Validation callback handler to transform submitted data for storage.
   */
  public function validateElement(array &$element, FormStateInterface $form_state, array &$complete_form): void {
    $values = $form_state->getValue($element['#parents']);

    unset($values['select']);
    unset($values['other']);

    if ($element['#translated_presets'] === TRUE) {
      // Store values in entity language.
      $langcode = $form_state->getValue(['langcode', 0, 'value'])
        ?? $form_state->getStorage()['langcode']
        ?? $form_state->getFormObject()->getEntity()->language();

      $translated_options = $this->getPresetOptions($element['#field_config'], $langcode);
      foreach ($values as $index => $value) {
        $values[$index] = $translated_options[$value] ?? $value;
      }
    }
    if (!$element['#multiple']) {
      $values = $values ? reset($values) : NULL;
    }

    $form_state->setValue($element['#parents'], $values);
  }

  /**
   * Determine property to which to apply select-or-other.
   *
   * Return NULL to not support a field type.
   */
  protected function getPresetProperty(FieldConfigInterface $fieldConfig): ?string {
    return match ($fieldConfig->getType()) {
      'phone_label' => 'title',
      'list_string' => NULL,
      default => $fieldConfig->getFieldStorageDefinition()->getMainPropertyName(),
    };
  }

  /**
   * Get the preset options in the provided language or untranslated if null.
   */
  protected function getPresetOptions(FieldConfigInterface $field_config, ?string $langcode = NULL): array {
    if ($langcode === NULL) {
      $config = $this->configFactory->get($field_config->getConfigDependencyName());
      $preset_options = $config->getOriginal(
        'third_party_settings.bsi_custom.preset_options',
        FALSE,
      );
    }
    else {
      // Always refer to the original translation before applying overrides.
      $preset_options = $this->getPresetOptions($field_config, NULL);
      $config_override = $this->configFactoryOverride->getOverride(
        $langcode,
        $field_config->getConfigDependencyName(),
      );
      foreach ($config_override->get('third_party_settings.bsi_custom.preset_options') ?? [] as $key => $label) {
        $preset_options[$key] = $label;
      }
    }
    return $preset_options;
  }

  /**
   * Implements hook_entity_view_alter().
   */
  #[Hook('entity_view_alter')]
  public function alterEntityView(array &$build, EntityInterface $entity, EntityViewDisplayInterface $display) : void {
    if ($entity->getEntityTypeId() !== 'media' || $entity->bundle() !== 'contact') {
      return;
    }
    foreach (array_keys($display->getComponents()) as $field_name) {
      /** @var \Drupal\Core\Field\FieldItemListInterface|null $items */
      $items = $entity->hasField($field_name) ? $entity->get($field_name) : NULL;
      if (!$items || $items->isEmpty()) {
        continue;
      }
      $field_config = $items->getFieldDefinition();
      if (!$field_config instanceof FieldConfig
        || !in_array('bsi_custom', $field_config->getThirdPartyProviders(), TRUE)) {
        continue;
      }

      $preset_options = $this->getPresetOptions($field_config, $items->getLangcode());

      foreach (Element::children($build[$field_name]) as $key) {
        $field_item = $items->get($key);
        $build[$field_name][$key]['#preset_option'] = array_search($field_item->title, $preset_options, TRUE);
        if ($build[$field_name][$key]['#preset_option'] === FALSE) {
          $build[$field_name][$key]['#preset_option'] = NULL;
        }
      }
    }
  }

}
