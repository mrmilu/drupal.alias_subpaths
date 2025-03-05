<?php

namespace Drupal\alias_subpaths_decoupled_router\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

class RouteSubscriber extends RouteSubscriberBase {

  /**
   * @inheritDoc
   */
  protected function alterRoutes(RouteCollection $collection) {
    if ($route = $collection->get('decoupled_router.path_translation')) {
      $route->setDefault('_disable_alias_subpaths', TRUE);
    }
  }

}
