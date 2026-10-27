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
    /** @var \Drupal\Core\Database\Query\Select $query */
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

    // Exclude ambiguous terms.
    $fields = $query->getFields();
    foreach ($fields as $key => $field) {
      $query->addExpression(
        sprintf('MIN(%s.%s)', $field['table'], $field['field']),
        $field['alias'] ?? NULL,
      );
      unset($fields[$key]);
    }
    $query->groupBy('tfd.name');
    $query->having('COUNT(tfd.name) = 1');
  }

}
