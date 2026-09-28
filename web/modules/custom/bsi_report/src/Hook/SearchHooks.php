<?php

namespace Drupal\bsi_report\Hook;

use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\NodeInterface;
use Drupal\views\ViewExecutable;

/**
 * Hooks for injecting the annual report search into report pages.
 */
class SearchHooks implements TrustedCallbackInterface {

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

    if (!in_array($node->bundle(), ['entry_page', 'report_page'], TRUE)) {
      return;
    }

    // The search view uses a contextual argument (node_book) to scope results
    // to the current book. Without a book ID, the view cannot function.
    if (empty($node->book['bid'])) {
      return;
    }

    $build['yearly_report_search'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['bsi-report-search'],
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

  /**
   * Implements hook_views_pre_render().
   *
   * Applies search form theming to the yearly report search view so the
   * exposed form uses the searchbar template (magnifying glass icon instead
   * of "Apply" button).
   */
  #[Hook('views_pre_render')]
  public function viewsPreRender(ViewExecutable $view): void {
    if ($view->id() !== 'search_yearly_report') {
      return;
    }

    $view->element['#pre_render'][] = [static::class, 'preRenderSearchForm'];
  }

  /**
   * Pre-render callback to theme the exposed form as a searchbar.
   */
  public static function preRenderSearchForm(array $build): array {
    if (empty($build['#exposed'])) {
      return $build;
    }

    $build['#exposed']['#theme_wrappers'] = ['form__search_global'];
    $build['#exposed']['search']['#theme'] = 'input__search';
    $build['#exposed']['actions']['submit']['#theme_wrappers'] = ['input__submit_search'];

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks(): array {
    return ['preRenderSearchForm'];
  }

}
