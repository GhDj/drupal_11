<?php

declare(strict_types=1);

namespace Drupal\bsi_csn;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\node\NodeInterface;

/**
 * Checks whether the current page is in the CSN context.
 */
final class CsnContextChecker {

  public function __construct(
    private readonly RouteMatchInterface $routeMatch,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Check if the current page belongs to the CSN group.
   */
  public function isCsnPage(): bool {
    // Check if the current route has a CSN group parameter.
    $group = $this->routeMatch->getParameter('group');
    if ($group && $group->bundle() === 'csn') {
      return TRUE;
    }

    // Check if the current node belongs to the CSN group.
    $node = $this->routeMatch->getParameter('node');
    if ($node instanceof NodeInterface) {
      /** @var \Drupal\group\Entity\Storage\GroupRelationshipStorageInterface $storage */
      $storage = $this->entityTypeManager->getStorage('group_relationship');
      foreach ($storage->loadByEntity($node) as $relationship) {
        if ($relationship->getGroup()->bundle() === 'csn') {
          return TRUE;
        }
      }
    }

    return FALSE;
  }

}
