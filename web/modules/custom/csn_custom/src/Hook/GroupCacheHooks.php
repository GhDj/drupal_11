<?php

declare(strict_types=1);

namespace Drupal\csn_custom\Hook;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\group\Entity\GroupInterface;
use Drupal\group\Entity\GroupRelationshipInterface;

/**
 * Handles cache invalidation based on group relationships.
 */
final class GroupCacheHooks {

  /**
   * Implements hook_ENTITY_TYPE_insert() for group_relationship.
   */
  #[Hook('group_relationship_insert')]
  public function onInsert(GroupRelationshipInterface $entity): void {
    $this->invalidate($entity);
  }

  /**
   * Implements hook_ENTITY_TYPE_insert() for group_relationship.
   */
  #[Hook('group_relationship_update')]
  public function onUpdate(GroupRelationshipInterface $entity): void {
    $this->invalidate($entity);
  }

  /**
   * Implements hook_ENTITY_TYPE_delete() for group_relationship.
   */
  #[Hook('group_relationship_delete')]
  public function onDelete(GroupRelationshipInterface $entity): void {
    $this->invalidate($entity);
  }

  /**
   * Implements hook_ENTITY_TYPE_delete() for group.
   */
  #[Hook('group_delete')]
  public function onGroupDelete(GroupInterface $entity): void {
    Cache::invalidateTags([
      'group_relations:' . $entity->id(),
    ]);
  }

  /**
   * Invalidate the caching for group relationship reliant items.
   */
  private function invalidate(GroupRelationshipInterface $entity): void {
    Cache::invalidateTags([
      'group_relations:' . $entity->getGroup()->id(),
    ]);
  }

}
