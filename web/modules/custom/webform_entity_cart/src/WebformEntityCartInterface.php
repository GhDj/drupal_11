<?php

namespace Drupal\webform_entity_cart;

use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Entity\EntityInterface;

/**
 * A service to manage the webform_entity_cart cart.
 */
interface WebformEntityCartInterface extends CacheableDependencyInterface {

  /**
   * Item count to ensure an item is present in the cart.
   *
   * Evaluates to the current count, or 1 if not contained yet.
   */
  public const int COUNT_ANY = 0;

  /**
   * Update cart for the given entity to the given count.
   */
  public function set(EntityInterface $entity, int $count = 1): bool;

  /**
   * Update cart to add the given entity a number of times.
   */
  public function add(EntityInterface $entity, int $count = self::COUNT_ANY): bool;

  /**
   * Update cart to remove the given entity (fully or partially).
   */
  public function remove(EntityInterface $entity, int $count = self::COUNT_ANY): bool;

  /**
   * Remove all items from cart.
   */
  public function clear(): void;

  /**
   * Get the cart data from request or cookie.
   *
   * @return array<string, array<int|string, int>>
   *   A nested map of [entity_type_id => [entity_id => count]].
   */
  public function getCart(): array;

}
