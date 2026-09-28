<?php

declare(strict_types=1);

namespace Drupal\webform_entity_cart;

use Drupal\Core\Cache\CacheableDependencyTrait;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Cookie-based implementation of a webform_entity_cart cart.
 */
final class CookieCart implements WebformEntityCartInterface {

  use CacheableDependencyTrait {
    getCacheContexts as traitGetCacheContexts;
  }

  public const string REQUEST_ATTR_CART = 'webform_entity_cart';
  public const string COOKIE_NAME = 'cart';
  public const int COOKIE_LIFETIME = 60 * 60 * 24 * 14;

  /**
   * Constructs a WebformEntityCartManager object.
   */
  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly RequestStack $requestStack,
  ) {}

  /**
   * {@inheritDoc}
   */
  public function set(EntityInterface $entity, int $count = 1): bool {
    $data = $this->getCart();
    $data[$entity->getEntityTypeId()][$entity->id()] = $count;
    return $this->setCart($data);
  }

  /**
   * {@inheritDoc}
   */
  public function add(EntityInterface $entity, int $count = self::COUNT_ANY): bool {
    $data = $this->getCart();
    if (!isset($data[$entity->getEntityTypeId()][$entity->id()])) {
      $data[$entity->getEntityTypeId()][$entity->id()] = 0;
    }

    $current_count = &$data[$entity->getEntityTypeId()][$entity->id()];
    $current_count = max($current_count + $count, 1);
    return $this->setCart($data);
  }

  /**
   * {@inheritDoc}
   */
  public function remove(EntityInterface $entity, int $count = self::COUNT_ANY): bool {
    $data = $this->getCart();
    if (!isset($data[$entity->getEntityTypeId()][$entity->id()])) {
      return TRUE;
    }

    $current_count = &$data[$entity->getEntityTypeId()][$entity->id()];
    $current_count = min($current_count - $count, 0);
    return $this->setCart($data);
  }

  /**
   * {@inheritDoc}
   */
  public function clear(): void {
    $this->setCart([]);
  }

  /**
   * {@inheritDoc}
   */
  public function getCart(): array {
    $cart = $this->requestStack->getCurrentRequest()->attributes->get(self::REQUEST_ATTR_CART)
      ?? $this->requestStack->getCurrentRequest()->cookies->get(self::COOKIE_NAME)
      ?? '';
    $data = $this->decode($cart);

    return $this->sanitizeCart($data);
  }

  /**
   * Prepare the cart data to be stored.
   */
  private function setCart(array $data): bool {
    $data = $this->sanitizeCart($data);
    // Store data on request, will be written to cookie later.
    // @see \Drupal\webform_entity_cart\EventSubscriber\WebformEntityCartSubscriber::onKernelResponse
    $this->requestStack->getCurrentRequest()->attributes->set(
      self::REQUEST_ATTR_CART,
      $this->encode($data)
    );

    return TRUE;
  }

  /**
   * Format cart data into something storable in a cookie.
   */
  private function encode(array $data): string {
    return base64_encode(json_encode($data));
  }

  /**
   * Parse cookie string into cart data.
   */
  private function decode(string $string): array {
    return json_decode(base64_decode($string, TRUE) ?: '', TRUE) ?: [];
  }

  /**
   * Perform sanity checks on the passed cart data.
   */
  private function sanitizeCart(mixed $data): array {
    $known_entity_types = array_keys($this->entityTypeManager->getDefinitions());
    $data = array_intersect_key($data, array_flip($known_entity_types));

    foreach ($data as $entity_type_id => $items) {
      $data[$entity_type_id] = array_filter($items, static fn ($count, $entity_id)
        => is_int($entity_id) && is_int($count) && $count > 0, ARRAY_FILTER_USE_BOTH);
      if ($data[$entity_type_id] === []) {
        unset($data[$entity_type_id]);
      }
    }

    return $data;
  }

  /**
   * {@inheritDoc}
   */
  public function getCacheContexts(): array {
    $contexts = $this->traitGetCacheContexts();
    $contexts[] = 'cookies:' . CookieCart::COOKIE_NAME;

    return $contexts;
  }

}
