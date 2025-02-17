<?php

namespace Drupal\alias_subpaths;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AliasSubpathsManager implements ContainerInjectionInterface {

  /**
   * @var \Drupal\alias_subpaths\AliasSubpathsAliasManager
   */
  private AliasSubpathsAliasManager $aliasSubpathsAliasManager;

  /**
   * @var \Drupal\alias_subpaths\ContextManager
   */
  private ContextManager $contextManager;

  /**
   * @var \Drupal\alias_subpaths\AliasSubpathsRouterManager
   */
  private AliasSubpathsRouterManager $aliasSubpathsRouterManager;

  /**
   * @param \Drupal\alias_subpaths\ContextManager $contextManager
   * @param \Drupal\alias_subpaths\AliasSubpathsAliasManager $aliasSubpathsAliasManager
   * @param \Drupal\alias_subpaths\AliasSubpathsRouterManager $aliasSubpathsRouterManager
   */
  public function __construct(
    AliasSubpathsAliasManager $aliasSubpathsAliasManager,
    ContextManager $contextManager,
    AliasSubpathsRouterManager $aliasSubpathsRouterManager
  ) {
    $this->aliasSubpathsAliasManager = $aliasSubpathsAliasManager;
    $this->contextManager = $contextManager;
    $this->aliasSubpathsRouterManager = $aliasSubpathsRouterManager;
  }

  /**
   * { @inheritdoc }
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('alias_subpaths.alias_manager'),
      $container->get('alias_subpaths.context_manager'),
      $container->get('alias_subpaths.router_manager')
    );
  }

  /**
   * @param $path
   *
   * @return array
   */
  public function resolve($path): array {
    $internal_path = $this->aliasSubpathsAliasManager->resolveUrl($path);
    $routeInfo = $this->getRouteInfo($path, $internal_path);
    $this->contextManager->processContextBag($path, $routeInfo['name']);

    return [
      'requested_path' => $path,
      'path' => $internal_path,
      'route' => $routeInfo,
      'params' => $this->contextManager->getContextBag($path)->getProcessedContent()
    ];
  }

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
