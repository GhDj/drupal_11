<?php

declare(strict_types=1);

namespace Drupal\media_remote_file\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Plugin implementation of the 'Remote Audio' formatter.
 */
#[FieldFormatter(
  id: 'remote_file_audio',
  label: new TranslatableMarkup('Remote Audio'),
  field_types: ['link'],
)]
class RemoteFileAudioFormatter extends RemoteFileVideoFormatter {

  /**
   * {@inheritDoc}
   */
  protected function getHtmlTag(): string {
    return 'audio';
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    $settings = parent::defaultSettings();
    unset($settings['muted'], $settings['playsinline'], $settings['poster_field']);
    return $settings;
  }

}
