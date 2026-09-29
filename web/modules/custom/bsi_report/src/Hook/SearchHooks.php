<?php

namespace Drupal\bsi_report\Hook;

use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Form\FormStateInterface;
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
        '#arguments' => [$node->book['bid']],
      ],
    ];
  }

  /**
   * Implements hook_form_views_exposed_form_alter().
   *
   * Applies searchbar theming to the yearly report search exposed form:
   * wraps form in bsi-searchbar, replaces input with search-styled input,
   * and replaces "Apply" button with magnifying glass icon.
   */
  #[Hook('form_views_exposed_form_alter')]
  public function formAlter(array &$form, FormStateInterface $form_state): void {
    $storage = $form_state->getStorage();
    $view = $storage['view'] ?? NULL;

    if (!$view || $view->id() !== 'search_yearly_report') {
      return;
    }

    $form['#theme_wrappers'] = ['form__search_global'];
    $form['search']['#theme'] = 'input__search';
    $form['actions']['submit']['#theme_wrappers'] = ['input__submit_search'];
  }

}
