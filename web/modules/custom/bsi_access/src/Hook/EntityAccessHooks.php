<?php

declare(strict_types=1);

namespace Drupal\bsi_access\Hook;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\node\Form\NodeForm;
use Drupal\node\NodeInterface;

/**
 * Entity operation hook handlers.
 */
final class EntityAccessHooks {

  /**
   * Allowed moderation states for node deletion.
   */
  public const ALLOWED_STATES = [
    'draft',
    'in_release',
    'deletion_request',
  ];

  /**
   * Implements hook_entity_operation_alter().
   */
  #[Hook('entity_operation_alter')]
  public function entityOperationAlter(array &$operations, EntityInterface $entity): void {

    if (!$entity instanceof NodeInterface) {
      return;
    }

    if (!$entity->hasField('moderation_state')) {
      return;
    }

    $state = $entity->get('moderation_state')->value;

    if (!in_array($state, self::ALLOWED_STATES, TRUE)) {
      unset($operations['delete']);
    }

  }

  /**
   * Removes the delete button from the node edit form when needed.
   */
  #[Hook('form_alter')]
  public function formAlter(array &$form, FormStateInterface $form_state, string $form_id): void {

    $form_object = $form_state->getFormObject();

    if (!$form_object instanceof NodeForm) {
      return;
    }

    $node = $form_object->getEntity();

    if (!$node->hasField('moderation_state')) {
      return;
    }

    $state = $node->get('moderation_state')->value;

    if (!in_array($state, self::ALLOWED_STATES, TRUE)) {
      unset($form['actions']['delete']);
    }

  }

}
