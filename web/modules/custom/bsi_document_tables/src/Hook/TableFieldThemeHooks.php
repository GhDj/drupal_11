<?php

declare(strict_types=1);

namespace Drupal\bsi_document_tables\Hook;

use Drupal\Core\Hook\Attribute\Hook;

/**
 * Theme hooks related to the TableField module.
 */
class TableFieldThemeHooks {

  /**
   * Implements hook_theme_suggestions_HOOK_alter() for table.
   */
  #[Hook('theme_suggestions_table_alter')]
  public function alterThemeSuggestions(array &$suggestions, array &$variables, string $hook): void {
    if (in_array('tablefield', $variables['attributes']['class'] ?? [])) {
      // @note We could parse $variables['attributes']['id'] for more info.
      // @see \Drupal\tablefield\Plugin\Field\FieldFormatter\TablefieldFormatter::viewElements
      $suggestions[] = $hook . '__tablefield';
    }
  }

}
