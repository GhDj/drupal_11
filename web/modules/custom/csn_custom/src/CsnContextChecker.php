<?php

declare(strict_types=1);

namespace Drupal\csn_custom;

use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Cache\CacheableDependencyTrait;
use Drupal\Core\Cache\RefinableCacheableDependencyTrait;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\group\Entity\GroupInterface;
use Drupal\group\Entity\Storage\GroupRelationshipStorageInterface;
use Drupal\user\UserInterface;

/**
 * Checks whether the current page is in the CSN context.
 */
final class CsnContextChecker implements CacheableDependencyInterface {
  use CacheableDependencyTrait;
  use RefinableCacheableDependencyTrait;

  /**
   * Holds cached results per entity.
   *
   * @var array<string, boolean>
   *   The cached results.
   */
  protected static array $cache = [];

  /**
   * The group_relationship storage.
   */
  private readonly GroupRelationshipStorageInterface $storage;

  public function __construct(
    private readonly RouteMatchInterface $routeMatch,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {
    $this->storage = $this->entityTypeManager->getStorage('group_relationship');
  }

  /**
   * Check if the current page belongs to the CSN group.
   *
   * Will use provided entities, with fallback to route match.
   */
  public function isCsnPage(?ContentEntityInterface $entity = NULL): bool {
    if ($this->routeMatch->getRouteName() === 'csn_custom.feedback') {
      $this->addCacheContexts(['route.name']);
      return TRUE;
    }

    if ($entity === NULL) {
      $this->addCacheContexts(['route']);

      foreach ($this->routeMatch->getParameters() as $parameter) {
        if ($parameter instanceof ContentEntityInterface && $this->isCsnContent($parameter)) {
          return TRUE;
        }
      }

      return FALSE;
    }
    else {
      return $this->isCsnContent($entity);
    }
  }

  /**
   * Whether the provided node belongs to a CSN group.
   */
  public function isCsnContent(ContentEntityInterface $entity): bool {
    if ($entity instanceof UserInterface) {
      // Ignore users for now, as they can be in many groups which would
      // display the whole page using CSN branding/elements.
      return FALSE;
    }
    elseif ($entity instanceof GroupInterface) {
      return $this->isCsnGroup($entity);
    }

    $cid = $entity->getEntityTypeId() . ':' . $entity->id();
    if (!isset(self::$cache[$cid])) {
      self::$cache[$cid] = FALSE;
      foreach ($this->storage->loadByEntity($entity) as $relationship) {
        if ($this->isCsnGroup($relationship->getGroup())) {
          $this->addCacheTags(['group_relations:' . $relationship->getGroupId()]);
          self::$cache[$cid] = TRUE;
          break;
        }
      }
    }
    return self::$cache[$cid];
  }

  /**
   * Determine if the provided group is a CSN group.
   */
  public function isCsnGroup(GroupInterface $group): bool {
    return $group->bundle() === 'csn';
  }

}
