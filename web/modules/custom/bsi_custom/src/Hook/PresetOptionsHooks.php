<?php

declare(strict_types=1);

namespace Drupal\bsi_custom\Hook;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\DependencyInjection\DependencySerializationTrait;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\FieldConfigInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\field\Entity\FieldConfig;
use Drupal\language\Config\LanguageConfigFactoryOverrideInterface;

/**
 * Hooks related to the fields and their formatters.
 *
 * Assumptions:
 * - Presets can be either a key-value map or a list, in which case
 *   the value of the original field config will be stored as the field value,
 *   even if they are translated using config_translation.
 * - Presets may be translated using config_translation on the field.
 *   This will only affect the displayed value, never the stored value.
 * - Field values may be translated using content_translation.
 *   This means, that different presets may be selected per language. If the
 *   same option is selected, the stored value will be identical.
 *
 * In short: Use config translation to change the *display* value, and content
 *   translation to change the *database* value.
 */
class PresetOptionsHooks {

  use StringTranslationTrait;
  use DependencySerializationTrait;

  public function __construct(
    protected readonly LanguageConfigFactoryOverrideInterface $configFactoryOverride,
    protected readonly ConfigFactoryInterface $configFactory,
    protected readonly LanguageManagerInterface $languageManager,
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
   * Determine property to which to apply select-or-other.
   *
   * Return NULL to not support a field type.
   */
  protected function getPresetProperty(FieldDefinitionInterface $field_config): ?string {
    return match ($field_config->getType()) {
      'phone_label' => 'title',
      'list_string' => NULL,
      default => $field_config->getFieldStorageDefinition()->getMainPropertyName(),
    };
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

    $interface_langcode = $this->languageManager
      ->getCurrentLanguage()
      ->getId();

    $property = $this->getPresetProperty($field_config);
    $preset_options = $this->getPresetOptions($field_config, $interface_langcode);
    $preset_other = $field_config->getThirdPartySetting('bsi_custom', 'preset_other', NULL);

    if (!isset($element[$property]) || (count($preset_options) == 0 && strlen($preset_other) === 0)) {
      return;
    }

    $default_value = $element[$property]['#default_value'];
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

    if (!$element['#multiple']) {
      $values = $values ? reset($values) : NULL;
    }

    $form_state->setValue($element['#parents'], $values);
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
      // Always refer to the original translation, then apply overrides.
      $base_options = $this->getPresetOptions($field_config, NULL);
      $config_override = $this->configFactoryOverride->getOverride(
        $langcode,
        $field_config->getConfigDependencyName(),
      );
      $translations = $config_override->get('third_party_settings.bsi_custom.preset_options') ?? [];

      $preset_options = [];
      foreach ($base_options as $key => $label) {
        $preset_options[$key] = $translations[$key] ?? $label;
      }
      if (array_is_list($preset_options)) {
        // Ensure presets are a map, keyed by storable data. Use base language
        // values as keys.
        $preset_options = array_combine($base_options, $preset_options);
      }
    }
    return $preset_options;
  }

  /**
   * Implements hook_entity_view_alter().
   */
  #[Hook('entity_view_alter')]
  public function alterEntityView(array &$build, EntityInterface $entity, EntityViewDisplayInterface $display) : void {
    foreach (array_keys($display->getComponents()) as $field_name) {
      /** @var \Drupal\Core\Field\FieldItemListInterface|null $items */
      $items = $entity->hasField($field_name) ? $entity->get($field_name) : NULL;
      if (!$items || $items->isEmpty()) {
        continue;
      }
      $field_config = $items->getFieldDefinition();
      if (!$field_config instanceof FieldConfig
        || !in_array('bsi_custom', $field_config->getThirdPartyProviders(), TRUE)
        || !$this->getPresetProperty($field_config)) {
        continue;
      }

      $this->alterFormatter($build[$field_name], $items, $display->getComponent($field_name));
    }
  }

  /**
   * Altering for the display of preset fields.
   */
  protected function alterFormatter(array &$element, FieldItemListInterface $items, array $component): void {
    $field_config = $items->getFieldDefinition();
    $property = $this->getPresetProperty($field_config);
    $preset_options = $this->getPresetOptions($field_config, $items->getLangcode());

    foreach ($items as $index => $item) {
      $value = $item->{$property};
      if ($value === NULL) {
        continue;
      }

      $label = $preset_options[$value] ?? $value;
      $element[$index]['#preset_option'] = array_key_exists($value, $preset_options) ? $value : NULL;
      $element[$index]['#preset_label'] = $label;
      if ($element[$index]['#preset_option'] === NULL) {
        continue;
      }

      // Update formatter render array for known plugins.
      if ($component['type'] === 'string') {
        $this->updateLabel($element[$index]['#context']['value'], $label);
      }
      elseif ($component['type'] === 'phone_label') {
        $this->updateLabel($element[$index]['#title'], $label);
      }
      elseif ($component['type'] === 'phone_link') {
        $this->updateLabel($element[$index]['#options']['attributes']['title'], $label);
      }
    }
  }

  /**
   * Helper to replace a render array value, accounting for translatable markup.
   */
  protected function updateLabel(&$value, $label): void {
    if ($value instanceof TranslatableMarkup) {
      $value = new TranslatableMarkup(
        // phpcs:ignore Drupal.Semantics.FunctionT.NotLiteralString
        $value->getUntranslatedString(),
        ['@title' => $label] + $value->getArguments(),
        $value->getOptions(),
      );
    }
    else {
      $value = $label;
    }
  }

}
