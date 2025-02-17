<?php

namespace Drupal\alias_subpaths;

use Drupal\Core\Entity\EntityInterface;
use Symfony\Component\Routing\RouterInterface;

class AliasSubpathsRouterManager {

  /**
   * @var \Symfony\Component\Routing\RouterInterface
   */
  private RouterInterface $router;

  /**
   * @param \Symfony\Component\Routing\RouterInterface $router
   */
  public function __construct(
    RouterInterface $router,
  ) {
    $this->router = $router;
  }

  /**
   * @param $path
   *
   * @return array
   */
  public function getRouteInfo($path): array {
    $route = $this->router->match($path);
    $arguments = [];
    foreach ($route as $param) {
      if ($param instanceof EntityInterface) {
        $arguments[] = $param;
      }
    }

    return [
      'name' => $route['_route'],
      'arguments' => $arguments,
    ];
  }

}
