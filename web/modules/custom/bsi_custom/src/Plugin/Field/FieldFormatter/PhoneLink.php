<?php

declare(strict_types=1);

namespace Drupal\bsi_custom\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\phone_label\Plugin\Field\FieldFormatter\PhoneLabelFormatter;

/**
 * Plugin implementation of the 'phone_link' formatter.
 */
#[FieldFormatter(
  id: 'phone_link',
  label: new TranslatableMarkup('Link'),
  field_types: ['phone_label'],
)]
class PhoneLink extends PhoneLabelFormatter {

  /**
   * {@inheritDoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $form = parent::settingsForm($form, $form_state);
    $form['title_tooltip'] = [
      '#type' => 'checkbox',
      '#title' => new TranslatableMarkup('Show title as hover tooltip'),
      '#default_value' => $this->getSetting('title_tooltip'),
    ];
    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public static function defaultSettings(): array {
    return parent::defaultSettings() + [
      'title_tooltip' => FALSE,
    ];
  }

  /**
   * {@inheritDoc}
   */
  public function settingsSummary(): array {
    $summary = parent::settingsSummary();
    if ($this->getSetting('title_tooltip')) {
      $summary[] = $this->t('Show title as hover tooltip');
    }
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $element = parent::viewElements($items, $langcode);

    foreach ($items as $delta => $item) {
      $element[$delta]['#title'] = $item->value;
      if ($item->title) {
        $element[$delta]['#options']['attributes']['title'] = $item->title;
      }
    }

    return $element;
  }

}
