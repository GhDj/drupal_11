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
   * Implements hook_page_attachments_alter().
   */
  #[Hook('page_attachments_alter')]
  public function alterPageAttachments(array &$attachments): void {
    if (in_array('editoria11y/editoria11y-localized', $attachments['#attached']['library'])) {
      $attachments['#attached']['library'][] = 'bsi_editor/editoria11y';
      $attachments['#attached']['drupalSettings']['bsi_editor'] = [
        'abbreviations_endpoint' => Url::fromRoute('bsi_editor.abbreviations_autocomplete')->toString(),
      ];
    }
  }

  /**
   * Implements hook_library_info_alter().
   */
  #[Hook('library_info_alter')]
  public function libraryInfoAlter(array &$libraries, string $extension): void {
    if ($extension === 'ckeditor5') {
      $libraries['internal.drupal.ckeditor5']['dependencies'][] = 'bsi_editor/ckeditor_abbreviation';
    }
  }

}
