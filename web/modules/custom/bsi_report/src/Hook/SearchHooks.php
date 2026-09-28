<?php

namespace Drupal\bsi_report\Hook;

use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\NodeInterface;

/**
 * Hooks for injecting the annual report search into report pages.
 */
class SearchHooks {

  use StringTranslationTrait;

  /**
   * Implements hook_ENTITY_TYPE_view() for node entities.
   *
   * Injects the yearly report search view into report_page nodes
   * that are part of a book. The search is scoped to the current
   * book via the view's contextual argument.
   */
  #[Hook('node_view')]
  public function nodeView(array &$build, NodeInterface $node, EntityViewDisplayInterface $display, string $view_mode): void {
    if ($view_mode !== 'full') {
      return;
    }

    if ($node->bundle() !== 'report_page') {
      return;
    }

    // Only show on nodes that are part of a book.
    if (empty($node->book['bid'])) {
      return;
    }

    $build['yearly_report_search'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['bsi-search-page__search'],
      ],
      '#weight' => -10,
      'heading' => [
        '#markup' => '<span class="bsi-searchbar__title">' . $this->t('Search annual report') . '</span>',
      ],
      'view' => [
        '#type' => 'view',
        '#name' => 'search_yearly_report',
        '#display_id' => 'block_search_yearly_reports',
      ],
    ];
  }

}
