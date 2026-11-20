<?php

declare(strict_types=1);

namespace Drupal\csn_custom\Hook;

use Drupal\csn_custom\CsnContextChecker;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Hook\Attribute\Hook;

/**
 * Theme-related hooks for the CSN module.
 */
class ThemeHooks {

  public function __construct(
    private readonly CsnContextChecker $contextChecker,
  ) {}

  /**
   * Implements hook_theme().
   */
  #[Hook('theme')]
  public function theme(): array {
    return [
      'csn_branding' => [
        'variables' => [
          'dashboard_url' => NULL,
        ],
      ],
      'csn_feedback' => [
        'variables' => [
          'title' => '',
          'message' => '',
          'content' => NULL,
        ],
      ],
      'csn_profile_form' => [
        'render element' => 'form',
        'template' => 'csn-profile-form',
      ],
    ];
  }

  /**
   * Implements hook_preprocess_HOOK() for menu--footer.html.twig.
   */
  #[Hook('preprocess_menu__footer')]
  public function preprocessFooterMenu(array &$variables): void {
    // Call context checker first to determine cache info.
    $is_csn = $this->contextChecker->isCsnPage();
    CacheableMetadata::createFromRenderArray($variables)
      ->addCacheableDependency($this->contextChecker)
      ->applyTo($variables);
    if (!$is_csn) {
      return;
    }

    foreach ($variables['items'] as &$item) {
      /** @var \Drupal\Core\Url $url */
      $url = $item['url'];
      $url->setOption('attributes', [
        'target' => '_blank',
      ] + ($url->getOption('attributes') ?? []));
    }
  }

}
