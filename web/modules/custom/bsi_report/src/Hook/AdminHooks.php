<?php

declare(strict_types=1);

namespace Drupal\bsi_report\Hook;

use Drupal\Core\Cache\RefinableCacheableDependencyInterface;
use Drupal\Core\Hook\Attribute\Hook;

/**
 * Hook implementations related to Jahreslagebericht.
 */
class AdminHooks {

  /**
   * Implements hook_menu_local_tasks_alter().
   */
  #[Hook('menu_local_tasks_alter')]
  public function alterLocalTasks(array &$data, string $route_name, RefinableCacheableDependencyInterface &$cacheability): void {
    if (isset($data['tabs'][0]['entity.node.book_outline_form'])) {
      // Remove the outline tab. Its content is available on the node form,
      // and it might confuse editors with book.admin_edit.
      unset($data['tabs'][0]['entity.node.book_outline_form']);
    }
  }

}
