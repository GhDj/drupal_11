<?php

namespace Drupal\bsi_media\Hook;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Hook\Attribute\Hook;

/**
 * Entity hooks related to media.
 */
final class EntityHooks {

  /**
   * Media types to prefill the publication date for.
   */
  private const BUNDLES = ['download', 'publication'];

  /**
   * Date field to update for media types.
   */
  private const FIELD = 'field_publication_date';

  /**
   * Implements hook_entity_view_alter().
   */
  #[Hook('entity_view_alter')]
  public function entityViewAlter(array &$build, EntityInterface $entity): void {
    if ($entity->getEntityTypeId() === 'media' && !$entity->access('update')) {
      unset($build['#contextual_links']);
    }
  }

  /**
   * Implements hook_media_presave().
   */
  #[Hook('media_presave')]
  public function mediaPresave(FieldableEntityInterface $entity) {
    if (!$entity->isNew() || !in_array($entity->bundle(), self::BUNDLES, TRUE)) {
      return;
    }

    if (!$entity->hasField(self::FIELD) || !$entity->get(self::FIELD)->isEmpty()) {
      return;
    }

    $created = date(
      'Y-m-d',
      $entity->getUntranslated()->getCreatedTime()
    );

    $entity->set(self::FIELD, $created);
  }

}
