<?php

namespace Drupal\alias_subpaths;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Manages alias subpaths resolution.
 *
 * This service integrates alias resolution, route information retrieval, and
 * context parameter processing.
 */
class AliasSubpathsManager implements ContainerInjectionInterface {

  /**
   * The alias subpaths alias manager.
   *
   * @var \Drupal\alias_subpaths\AliasSubpathsAliasManager
   */
  private AliasSubpathsAliasManager $aliasSubpathsAliasManager;

  /**
   * The context manager service.
   *
   * @var \Drupal\alias_subpaths\ContextManager
   */
  private ContextManager $contextManager;

  /**
   * The alias subpaths router manager.
   *
   * @var \Drupal\alias_subpaths\AliasSubpathsRouterManager
   */
  private AliasSubpathsRouterManager $aliasSubpathsRouterManager;

  /**
   * Constructs a new AliasSubpathsManager.
   *
   * @param \Drupal\alias_subpaths\AliasSubpathsAliasManager $aliasSubpathsAliasManager
   *   The alias manager for alias subpaths.
   * @param \Drupal\alias_subpaths\ContextManager $contextManager
   *   The context manager service.
   * @param \Drupal\alias_subpaths\AliasSubpathsRouterManager $aliasSubpathsRouterManager
   *   The router manager service.
   */
  public function __construct(
    AliasSubpathsAliasManager $aliasSubpathsAliasManager,
    ContextManager $contextManager,
    AliasSubpathsRouterManager $aliasSubpathsRouterManager,
  ) {
    $this->aliasSubpathsAliasManager = $aliasSubpathsAliasManager;
    $this->contextManager = $contextManager;
    $this->aliasSubpathsRouterManager = $aliasSubpathsRouterManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('alias_subpaths.alias_manager'),
      $container->get('alias_subpaths.context_manager'),
      $container->get('alias_subpaths.router_manager')
    );
  }

  /**
   * Resolves the given path to its internal system path and route info.
   *
   * This method uses the alias manager to resolve the URL, retrieves the route
   * information, processes context parameters, and returns an array containing
   * the requested path, the resolved internal path, route information, and any
   * processed parameters.
   *
   * @param string $path
   *   The external URL path.
   *
   * @return array
   *   An associative array with the following keys:
   *   - requested_path: The original URL path.
   *   - path: The resolved internal system path.
   *   - route: The route information.
   *   - params: The processed context parameters.
   */
  public function resolve($path): array {
    $internal_path = $this->aliasSubpathsAliasManager->resolveUrl($path);
    $routeInfo = $this->getRouteInfo($path, $internal_path);
    $this->contextManager->processContextBag($path);

    return [
      'requested_path' => $path,
      'path' => $internal_path,
      'route' => $routeInfo,
      'params' => $this->contextManager->getContextBag($path)->getProcessedContent(),
    ];
  }

  /**
   * Retrieves route information for the given path.
   *
   * If route information is already stored in the context bag, it is returned;
   * otherwise, it is retrieved via the router manager and then stored.
   *
   * @param string $path
   *   The original URL path.
   * @param string $internal_path
   *   The resolved internal system path.
   *
   * @return array
   *   An array containing route information.
   */
  private function getRouteInfo($path, $internal_path) {
    $contextBag = $this->contextManager->getContextBag($path);
    if ($contextBag->getRouteInfo()) {
      return $contextBag->getRouteInfo();
    }
    $routeInfo = $this->aliasSubpathsRouterManager->getRouteInfo($internal_path);
    $contextBag->setRouteInfo($routeInfo);
    return $routeInfo;
  }

}
