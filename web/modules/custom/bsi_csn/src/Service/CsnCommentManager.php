<?php

declare(strict_types=1);

namespace Drupal\bsi_csn\Service;

use Drupal\Core\Database\Connection;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeInterface;

/**
 * Manages CSN comments on Vorfallsbericht nodes.
 */
final class CsnCommentManager {

  /**
   * The maximum allowed length for comment body text.
   */
  private const int MAX_BODY_LENGTH = 1000;

  public function __construct(
    private readonly Connection $database,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Creates a comment on a Vorfallsbericht node.
   *
   * @return array
   *   The created comment record.
   *
   * @throws \InvalidArgumentException
   *   If the body exceeds the character limit.
   */
  public function createComment(NodeInterface $node, AccountInterface $user, string $authorRole, string $body): array {
    if (mb_strlen($body) > self::MAX_BODY_LENGTH) {
      throw new \InvalidArgumentException('Comment body must not exceed 1000 characters.');
    }
    if (trim($body) === '') {
      throw new \InvalidArgumentException('Comment body must not be empty.');
    }

    $created = \Drupal::time()->getRequestTime();

    $id = (int) $this->database->insert('csn_comment')
      ->fields([
        'nid' => $node->id(),
        'uid' => $user->id(),
        'author_role' => $authorRole,
        'body' => $body,
        'created' => $created,
      ])
      ->execute();

    // When a CSN-Admin sends a correction, update the report status.
    if ($authorRole === 'csn_admin') {
      $this->setNodeCorrectionStatus($node, 'in_correction');
    }

    return [
      'id' => $id,
      'nid' => (int) $node->id(),
      'uid' => (int) $user->id(),
      'author_role' => $authorRole,
      'body' => $body,
      'created' => $created,
    ];
  }

  /**
   * Loads all comments for a given Vorfallsbericht node.
   *
   * @return array
   *   Array of comment records ordered by creation time.
   */
  public function getComments(NodeInterface $node): array {
    $results = $this->database->select('csn_comment', 'c')
      ->fields('c')
      ->condition('c.nid', $node->id())
      ->orderBy('c.created', 'ASC')
      ->execute()
      ->fetchAll(\PDO::FETCH_ASSOC);

    return $results ?: [];
  }

  /**
   * Determines the CSN author role for a user on a given node.
   *
   * @return string|null
   *   'csn_admin' or 'ersthelfer', or NULL if the user has no CSN role.
   */
  public function determineAuthorRole(NodeInterface $node, AccountInterface $user): ?string {
    // Site-wide CSN admin role takes precedence.
    if ($user->hasRole('csn_admin')) {
      return 'csn_admin';
    }

    /** @var \Drupal\group\Entity\Storage\GroupRelationshipStorageInterface $storage */
    $storage = $this->entityTypeManager->getStorage('group_relationship');
    foreach ($storage->loadByEntity($node) as $relationship) {
      $group = $relationship->getGroup();
      if ($group->bundle() !== 'csn') {
        continue;
      }
      $member = $group->getMember($user);
      if ($member === FALSE) {
        continue;
      }
      foreach ($member->getRoles() as $role) {
        if ($role->id() === 'csn-admin') {
          return 'csn_admin';
        }
      }
      // User is a CSN group member but not admin → Ersthelfer.
      return 'ersthelfer';
    }

    return NULL;
  }

  /**
   * Sets the correction status field on a Vorfallsbericht node.
   */
  private function setNodeCorrectionStatus(NodeInterface $node, string $status): void {
    if (!$node->hasField('field_correction_status')) {
      return;
    }
    $node->set('field_correction_status', $status);
    $node->save();
  }

}
