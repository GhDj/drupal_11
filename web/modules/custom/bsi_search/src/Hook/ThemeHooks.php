<?php

namespace Drupal\bsi_search\Hook;

use Drupal\config_pages\ConfigPagesLoaderServiceInterface;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Theme hooks for the BSI Search module.
 */
class ThemeHooks {

  use StringTranslationTrait;

  public function __construct(
    #[Autowire(service: 'config_pages.loader')]
    protected readonly ConfigPagesLoaderServiceInterface $configPagesLoader,
  ) {}

  /**
   * Implements hook_preprocess_block().
   */
  #[Hook('preprocess_block')]
  public function preprocessBlock(&$variables): void {
    if (($variables['elements']['#id'] ?? NULL) !== 'bsi_bund_site_search') {
      return;
    }

    $config_page = $this->configPagesLoader->load('page_frame');
    if (!$config_page) {
      $variables['#cache']['tags'][] = 'config_pages_list:page_frame';
      return;
    }

    // Show search quick links for block in header region.
    $variables['content']['suggestions'] = $config_page->get('field_suggestions')
      ->view('default');

    CacheableMetadata::createFromRenderArray($variables)
      ->addCacheableDependency($config_page)
      ->applyTo($variables);
  }

}
