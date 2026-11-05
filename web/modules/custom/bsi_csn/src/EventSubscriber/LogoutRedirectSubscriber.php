<?php

declare(strict_types=1);

namespace Drupal\bsi_csn\EventSubscriber;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Redirects CSN users to the public login page after logout.
 */
final class LogoutRedirectSubscriber implements EventSubscriberInterface {

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    // Run after the default logout handler.
    return [KernelEvents::RESPONSE => ['onResponse', -100]];
  }

  /**
   * Redirect logout responses to the configured external URL.
   */
  public function onResponse(ResponseEvent $event): void {
    $request = $event->getRequest();
    if ($request->getRequestUri() !== '/user/logout') {
      return;
    }

    $response = $event->getResponse();
    if (!$response->isRedirection()) {
      return;
    }

    $redirect_url = $this->configFactory->get('bsi_csn.settings')->get('logout_redirect_url');
    if (!$redirect_url) {
      return;
    }

    $event->setResponse(new TrustedRedirectResponse($redirect_url));
  }

}
