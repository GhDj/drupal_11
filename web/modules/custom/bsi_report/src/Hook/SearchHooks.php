<?php

namespace Drupal\bsi_report\Hook;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;

/**
 * Hooks for the annual report search.
 */
class SearchHooks {

  /**
   * Implements hook_form_views_exposed_form_alter().
   *
   * Applies searchbar theming to the yearly report search exposed form.
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
