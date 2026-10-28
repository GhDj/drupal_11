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

  /**
   * Implements hook_preprocess_views_view().
   */
  #[Hook('preprocess_views_view_unformatted')]
  public function preprocessViewsViewUnformatted(array &$variables): void {
    $view = $variables['view'];

    if ($view->id() !== 'search_events') {
      return;
    }

    if ($view->current_display !== 'block_events') {
      return;
    }

    $days = [];

    foreach ($view->result as $index => $row) {
      $entity = $row->_entity ?? NULL;

      if (!$entity || $entity->get('field_date_range')->isEmpty()) {
        continue;
      }

      $start = new \DateTime(
        $entity->get('field_date_range')->value
      );

      $end = new \DateTime(
        $entity->get('field_date_range')->end_value
          ?: $entity->get('field_date_range')->value
      );

      $current = clone $start;

      while ($current <= $end) {
        $key = $current->format('Y-m-d');

        $days[$key]['date'] = clone $current;
        $days[$key]['rows'][] = $variables['rows'][$index];

        $current->modify('+1 day');
      }
    }

    ksort($days);

    $variables['event_days'] = $days;
  }

}
