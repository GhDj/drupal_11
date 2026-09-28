<?php

namespace Drupal\bsi_access\Access;

use Drupal\bsi_access\Hook\EntityAccessHooks;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\NodeInterface;

/**
 * Provides moderation-state based access control for node deletion.
 */
class DeleteNodeAccessCheck implements AccessInterface {

  /**
   * Checks whether the node may be deleted.
   */
  public function access(NodeInterface $node, AccountInterface $account): AccessResult {

    if ($node->hasField('moderation_state')) {
      $state = $node->get('moderation_state')->value;

      if (!in_array($state, EntityAccessHooks::ALLOWED_STATES, TRUE)) {
        return AccessResult::forbidden()
          ->addCacheableDependency($node);
      }
    }

    return AccessResult::allowed()->addCacheableDependency($node);
  }

}
