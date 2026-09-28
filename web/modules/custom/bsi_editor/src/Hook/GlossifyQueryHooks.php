<?php

declare(strict_types=1);

namespace Drupal\bsi_editor\Hook;

use Drupal\Core\Database\Query\AlterableInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Language\LanguageInterface;

/**
 * Query hooks related to the Glossify module.
 */
class GlossifyQueryHooks {

  /**
   * Implements hook_query_TAG_alter() for glossify_taxonomy_tooltip.
   *
   * Add support for language neutral taxonomies.
   */
  #[Hook('query_glossify_taxonomy_tooltip_alter')]
  public function alterTermQuery(AlterableInterface $query): void {
    $conditions = &$query->conditions();
    foreach ($conditions as $key => $condition) {
      if (isset($condition['field']) && $condition['field'] === 'tfd.langcode') {
        $conditions[$key]['operator'] = 'IN';
        $conditions[$key]['value'] = [
          $condition['value'],
          LanguageInterface::LANGCODE_NOT_SPECIFIED,
          LanguageInterface::LANGCODE_NOT_APPLICABLE,
        ];

        break;
      }
    }
  }

}
