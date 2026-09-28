<?php

declare(strict_types=1);

namespace Drupal\bsi_media\Hook;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Session\AccountInterface;

/**
 * Hooks related to media download functionality.
 */
class MediaDownloadHooks {

  /**
   * Implements hook_ENTITY_TYPE_access() for media.
   */
  #[Hook('media_access')]
  public function downloadAccess(EntityInterface $entity, string $operation, AccountInterface $account): AccessResultInterface {
    if ($operation === 'download') {
      $permission = 'download ' . $entity->bundle() . ' media';

      return AccessResult::allowedIfHasPermission($account, $permission)
        ->cachePerPermissions()
        ->addCacheableDependency($entity);
    }

    return AccessResult::neutral();
  }

}
