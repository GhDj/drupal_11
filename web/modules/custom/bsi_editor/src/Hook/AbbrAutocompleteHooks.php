<?php

declare(strict_types=1);

namespace Drupal\bsi_editor\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Url;

/**
 * Hook implementations using attributes.
 */
class AbbrAutocompleteHooks {

  /**
   * Attaches required libraries and JavaScript settings.
   */
  #[Hook('page_attachments')]
  public function pageAttachments(array &$attachments): void {
    if (!\Drupal::currentUser()->hasPermission('view editoria11y checker')) {
      return;
    }
    $route_match = \Drupal::routeMatch();
    $route_name = $route_match->getRouteName();
    if (str_starts_with($route_name, 'layout_builder.')) {
      // Do not load library on layout builder routes.
      return;
    }

    $attachments['#attached']['library'][] = 'bsi_editor/editoria11y';
    $attachments['#attached']['drupalSettings']['bsi_editor'] = [
      'abbreviations_endpoint' => Url::fromRoute('bsi_editor.abbreviations_autocomplete')->toString(),
    ];

    // @todo This does not need to be attached globally, load with ckeditor.
    $attachments['#attached']['library'][] = 'bsi_editor/ckeditor_abbreviation';
  }

}
