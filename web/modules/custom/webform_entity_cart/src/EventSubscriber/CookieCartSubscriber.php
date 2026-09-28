<?php

declare(strict_types=1);

namespace Drupal\webform_entity_cart\EventSubscriber;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Cache\CacheableResponseInterface;
use Drupal\webform_entity_cart\CookieCart;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Subscriber for handling cart contents.
 */
final class CookieCartSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::RESPONSE => ['onKernelResponse'],
    ];
  }

  /**
   * Kernel response event handler.
   */
  public function onKernelResponse(ResponseEvent $event): void {
    $cart = $event->getRequest()->attributes->get(CookieCart::REQUEST_ATTR_CART, []);
    if ($cart) {
      $cookie = new Cookie(CookieCart::COOKIE_NAME, $cart,
        expire: time() + CookieCart::COOKIE_LIFETIME,
        path: '/',
        httpOnly: FALSE,
      );

      $response = $event->getResponse();
      $response->headers->setCookie($cookie);
      if ($response instanceof CacheableResponseInterface) {
        $response->addCacheableDependency((new CacheableMetadata())->addCacheContexts([
          'cookies:' . CookieCart::COOKIE_NAME,
        ]));
      }
    }
  }

}
