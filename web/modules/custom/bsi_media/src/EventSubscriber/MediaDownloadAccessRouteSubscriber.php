<?php

declare(strict_types=1);

namespace Drupal\bsi_media\EventSubscriber;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Route subscriber.
 */
final class MediaDownloadAccessRouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection): void {
    if ($route = $collection->get('media_entity_download.download')) {
      // @todo Also alter path: '/media/{media}/download'?
      $route->setRequirement('_entity_access', 'media.download');
    }
  }

}
