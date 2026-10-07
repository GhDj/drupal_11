<?php

namespace Drupal\bsi_media\Hook;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Hook\Attribute\Hook;

/**
 * Entity hooks related to media.
 */
final class EntityHooks {

  /**
   * Implements hook_entity_view_alter().
   */
  #[Hook('entity_view_alter')]
  public function entityViewAlter(array &$build, EntityInterface $entity): void {
    if ($entity->getEntityTypeId() === 'media' && !$entity->access('update')) {
      unset($build['#contextual_links']);
    }
  }

}
