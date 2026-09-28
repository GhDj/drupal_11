<?php

namespace Drupal\bsi_access\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Adds additional access requirements to the node delete route.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * Alters routes.
   */
  protected function alterRoutes(RouteCollection $collection): void {
    if ($route = $collection->get('entity.node.delete_form')) {
      $route->setRequirement(
        '_bsi_access_delete_node_access',
        'TRUE'
      );
    }
  }

}
