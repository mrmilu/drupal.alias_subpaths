<?php

namespace Drupal\alias_subpaths;

use Drupal\path_alias\AliasManagerInterface;

/**
 * Manages alias subpaths resolution.
 *
 * This class is responsible for resolving URL paths using the alias manager
 * and processing context arguments via the context manager. It attempts to find
 * the system path corresponding to a given alias by progressively reducing
 * the path until a valid system path is found.
 */
class AliasSubpathsAliasManager {

  /**
   * The alias manager for looking up the system path.
   *
   * @var \Drupal\path_alias\AliasManagerInterface
   */
  protected $aliasManager;

  /**
   * The context manager service.
   *
   * @var \Drupal\alias_subpaths\ContextManager
   */
  private ContextManager $contextManager;

  /**
   * @var \Drupal\alias_subpaths\UnlocalizeUrlService
   */
  private UnlocalizeUrlService $unlocalizeUrlService;

  /**
   * Constructs a new AliasSubpathsAliasManager.
   *
   * @param \Drupal\path_alias\AliasManagerInterface $alias_manager
   *   An alias manager for looking up the system path.
   * @param \Drupal\alias_subpaths\ContextManager $context_manager
   *   The context manager service.
   * @param \Drupal\alias_subpaths\UnlocalizeUrlService $unlocalize_url_service
   */
  public function __construct(AliasManagerInterface $alias_manager, ContextManager $context_manager, UnlocalizeUrlService $unlocalize_url_service) {
    $this->aliasManager = $alias_manager;
    $this->contextManager = $context_manager;
    $this->unlocalizeUrlService = $unlocalize_url_service;
  }

  /**
   * Resolves the given URL path to its system path.
   *
   * This method uses the context manager to retrieve or create a context bag
   * for the provided path. If the context bag already contains a resolved path,
   * that path is returned. Otherwise, the method attempts to resolve the path
   * by progressively reducing the alias and checking for a corresponding system
   * path. Any extracted arguments are added to the context bag.
   *
   * @param string $path
   *   The URL path to resolve.
   *
   * @return string
   *   The resolved system path.
   */
  public function resolveUrl($path) {
    $path = $this->unlocalizeUrlService->unlocalizeUrl($path);
    $contextBag = $this->contextManager->getContextBag($path);
    if ($contextBag->getPath()) {
      return $contextBag->getPath();
    }

    $path_parts = explode('/', trim($path, '/'));

    while (count($path_parts) > 0) {
      $current_alias = '/' . implode('/', $path_parts);
      $current_path = $this->aliasManager->getPathByAlias($current_alias);
      if ($current_path !== $current_alias) {
        $contextBag->setPath($current_path);
        return $current_path;
      }
      $argument = array_pop($path_parts);
      $contextBag->add($argument);
    }

    return $contextBag->setPath($path);
  }

}
