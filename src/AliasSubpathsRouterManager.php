<?php

namespace Drupal\alias_subpaths;

use Drupal\Core\Entity\EntityInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Manages the retrieval of route information based on a given path.
 *
 * This class uses Drupal's routing system to match a provided path and extract
 * the route name along with any entity arguments present in the route.
 */
class AliasSubpathsRouterManager {

  /**
   * The router service.
   *
   * @var \Symfony\Component\Routing\RouterInterface
   */
  private RouterInterface $router;

  /**
   * Constructs a new AliasSubpathsRouterManager.
   *
   * @param \Symfony\Component\Routing\RouterInterface $router
   *   The router service used to match paths.
   */
  public function __construct(
    RouterInterface $router,
  ) {
    $this->router = $router;
  }

  /**
   * Retrieves route information for the given path.
   *
   * This method matches the provided path to a route and extracts the route
   * name along with any entity arguments found in the route parameters.
   *
   * @param string $path
   *   The path for which to retrieve route information.
   *
   * @return array
   *   An associative array containing:
   *   - 'name': The name of the matched route.
   *   - 'arguments': An array of entity arguments extracted from the route.
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
