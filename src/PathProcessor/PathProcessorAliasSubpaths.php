<?php

namespace Drupal\alias_subpaths\PathProcessor;

use Drupal\alias_subpaths\AliasSubpathsAliasManager;
use Drupal\alias_subpaths\Exception\NotRouteApplicableException;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\PathProcessor\InboundPathProcessorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Processes the inbound path using path alias lookups.
 */
class PathProcessorAliasSubpaths implements InboundPathProcessorInterface {

  /**
   * The alias subpaths alias manager.
   *
   * @var \Drupal\alias_subpaths\AliasSubpathsAliasManager
   */
  private AliasSubpathsAliasManager $aliasSubpathsAliasManager;

  /**
   * Module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  private ModuleHandlerInterface $moduleHandler;

  /**
   * Dependency injection.
   *
   * @var \Symfony\Component\DependencyInjection\ContainerInterface
   */
  private ContainerInterface $container;

  /**
   * Constructs a new PathProcessorAliasSubpaths.
   *
   * @param \Drupal\alias_subpaths\AliasSubpathsAliasManager $alias_subpaths_url_resolver
   *   The alias subpaths alias manager service.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The Module Handler service.
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   For dependency injection.
   */
  public function __construct(
    AliasSubpathsAliasManager $alias_subpaths_url_resolver,
    ModuleHandlerInterface $module_handler,
    ContainerInterface $container,
  ) {
    $this->aliasSubpathsAliasManager = $alias_subpaths_url_resolver;
    $this->moduleHandler = $module_handler;
    $this->container = $container;
  }

  /**
   * {@inheritdoc}
   */
  public function processInbound($path, Request $request) {
    if ($request->attributes->get('_disable_alias_subpaths')) {
      return $path;
    }

    if ($this->moduleHandler->moduleExists('redirect')) {
      if ($this->container->has('redirect.repository')) {
        $redirectRepository = $this->container->get('redirect.repository');
        $sourcePath = trim($path, '/');
        $redirects = $redirectRepository->findBySourcePath($sourcePath);

        $redirect = reset($redirects);
        if ($redirect) {
          return $path;
        }
      }
    }
    try {
      return $this->aliasSubpathsAliasManager->resolveUrl($path);
    }
    catch (NotRouteApplicableException $e) {
      return $path;
    }
  }

}
