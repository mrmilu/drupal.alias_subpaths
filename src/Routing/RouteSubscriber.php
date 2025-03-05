<?php

namespace Drupal\alias_subpaths\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

class RouteSubscriber extends RouteSubscriberBase {

  /**
   * @inheritDoc
   */
  protected function alterRoutes(RouteCollection $collection) {
    if ($route = $collection->get('image.style_public')) {
      $route->setDefault('_disable_alias_subpaths', TRUE);
    }
    if ($route = $collection->get('image.style_private')) {
      $route->setDefault('_disable_alias_subpaths', TRUE);
    }

    foreach ($collection->all() as $route_name => $route) {
      if (
        str_starts_with($route_name, 'system.')
        || str_starts_with($route_name, 'view.')
        || $route->getOption('_admin_route')
        || ($route->getPath() === '/')
      ) {
        $route->setDefault('_disable_alias_subpaths', TRUE);
      }
    }
  }

}
