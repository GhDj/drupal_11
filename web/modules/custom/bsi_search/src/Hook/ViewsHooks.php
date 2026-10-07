<?php

namespace Drupal\bsi_search\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\views\Plugin\views\area\Result;
use Drupal\views\ViewExecutable;

/**
 * Views hooks for the BSI Search module.
 */
class ViewsHooks {

  use StringTranslationTrait;

  /**
   * Implements hook_views_pre_render().
   */
  #[Hook('views_pre_render')]
  public function preRender(ViewExecutable $view): void {
    // We do not have access to the search term in the results summary view
    // header so we use the preRender hook of the view to append the search
    // term to it.
    $views = [
      'search',
      'search_lists',
      'search_pages',
      'search_media',
      'search_yearly_report',
    ];
    $displays = [
      'block_media',
      'block_search_article',
      'block_search_bits',
      'block_search_global',
      'block_search_publication',
      'block_search_yearly_reports',
      'block_search_security_label',
    ];

    if (!in_array($view->id(), $views, TRUE) && !in_array($view->current_display, $displays, TRUE)) {
      return;
    }

    // Get exposed input.
    $input = $view->getExposedInput();
    $term = $input['search'] ?? '';

    // Only append if a term exists.
    if (!empty($term)) {
      /** @var Result $handler */
      foreach ($view->display_handler->getHandlers('header') as $delta => $handler) {
        if ($handler instanceof Result) {
          $summary = $handler->options['content'];
          if (!empty($summary)) {
            $view->display_handler->getHandlers('header')[$delta]->options['content'] .= ' ' . $this->t("for your search for %term", ['%term' => $term], ['context' => 'search term']);
          }
        }
      }
    }
  }

  /**
   * Implements hook_views_data().
   */
  #[Hook('views_data')]
  public function viewsData(): array {
    $data = [];

    $data['node_field_data']['effective_publication_date'] = [
      'title' => $this->t('Effective publication date'),
      'help' => $this->t('Sort using publication date, media created date or node created date.'),
      'sort' => [
        'id' => 'effective_publication_date',
      ],
    ];

    return $data;
  }

}
