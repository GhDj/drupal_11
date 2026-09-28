<?php

namespace Drupal\bsi_report\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Hooks for views integration of annual report books.
 */
class ViewsHooks {

  use StringTranslationTrait;

  /**
   * Implements hook_views_data_alter().
   */
  #[Hook('views_data_alter')]
  public function viewsDataAlter(array &$data): void {
    if (isset($data['book'])) {
      $data['book']['pid']['argument'] = [
        'id' => 'standard',
      ];
    }
    if (isset($data['menu_entity_index'])) {
      $data['menu_entity_index']['children']['relationship'] = [
        'title' => $this->t('Children'),
        'label' => $this->t('Menu link children'),
        'id' => 'standard',
        'base' => 'menu_entity_index',
        'base field' => 'parent_id',
        'field' => 'entity_id',
        'join_extra' => [
          [
            'field' => 'parent_type',
            'left_field' => 'entity_type',
          ],
        ],
      ];
    }
  }

}
