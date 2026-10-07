<?php

declare(strict_types=1);

namespace Drupal\bsi_custom\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\StringFormatter;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\OptGroup;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\filter\FilterFormatInterface;

/**
 * Plugin implementation of the 'Text with text format' formatter.
 */
#[FieldFormatter(
  id: 'plain_text_formatted',
  label: new TranslatableMarkup('Text with text format'),
  field_types: ['string', 'string_long', 'list_string'],
)]
class PlainTextFormattedFormatter extends StringFormatter {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return [
      'text_format' => filter_default_format(),
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $form['text_format'] = [
      '#type' => 'select',
      '#title' => $this->t('Text format'),
      '#options' => array_map(
        static fn(FilterFormatInterface $format) => $format->label(),
        filter_formats()
      ),
      '#default_value' => $this->getSetting('text_format'),
    ];

    return $form + parent::settingsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    $summary = parent::settingsSummary();
    array_unshift($summary, $this->t('@label: @value', [
      '@label' => $this->t('Text format'),
      '@value' => filter_formats()[$this->getSetting('text_format')]?->label(),
    ]));
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  protected function viewValue(FieldItemInterface $item): array {
    // The text value has no text format assigned to it, so the user input
    // should equal the output, including newlines.
    $value = $this->getValue($item);
    return [
      '#type' => 'processed_text',
      '#text' => $value,
      '#format' => $this->getSetting('text_format'),
      '#langcode' => $item->getLangcode(),
    ];
  }

  /**
   * Extract value to display.
   */
  protected function getValue(FieldItemInterface $item): mixed {
    $value = $item->value ?? '';
    if ($this->fieldDefinition->getType() === 'list_string') {
      $provider = $item->getFieldDefinition()
        ->getFieldStorageDefinition()
        ->getOptionsProvider('value', $item->getEntity());
      // Flatten the possible options, to support opt groups.
      $options = OptGroup::flattenOptions($provider->getPossibleOptions());
      $value = $options[$value] ?? $value;
    }

    return $value;
  }

}
