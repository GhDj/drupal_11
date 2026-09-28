<?php

declare(strict_types=1);

namespace Drupal\media_remote_file\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Theme hooks for the media_remote_file module.
 */
class MediaRemoteFileThemeHooks {

  use StringTranslationTrait;

  /**
   * Implements hook_theme().
   */
  #[Hook('theme')]
  public function theme(): array {
    return [
      'remote_file_audio' => [
        'variables' => [
          'sources' => [],
          'tracks' => [],
          'attributes' => NULL,
        ],
      ],
      'remote_file_video' => [
        'variables' => [
          'sources' => [],
          'poster' => NULL,
          'tracks' => [],
          'attributes' => NULL,
        ],
      ],
    ];
  }

}
