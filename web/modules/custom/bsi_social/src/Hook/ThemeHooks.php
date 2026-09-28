<?php

namespace Drupal\bsi_social\Hook;

use Drupal\Core\Hook\Attribute\Hook;

/**
 * Theme hooks for the BSI Social module.
 */
final class ThemeHooks {

  /**
   * Implements hook_theme().
   */
  #[Hook('theme')]
  public function theme($existing, $type, $theme, $path): array {
    return [
      'mastodon_posts' => [
        'variables' => [
          'items' => [],
        ],
        'template' => 'mastodon-posts',
      ],
    ];
  }

}
