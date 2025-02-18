<?php

namespace Drupal\alias_subpaths;

use Drupal\path_alias\AliasManagerInterface;

/**
 *
 */
class AliasSubpathsAliasManager {

  /**
   * An alias manager for looking up the system path.
   *
   * @var \Drupal\path_alias\AliasManagerInterface
   */
  protected $aliasManager;

  /**
   * @var \Drupal\alias_subpaths\ContextManager
   */
  private ContextManager $contextManager;

  /**
   * Constructs a AliasPathProcessor object.
   *
   * @param \Drupal\path_alias\AliasManagerInterface $alias_manager
   *   An alias manager for looking up the system path.
   */
  public function __construct(AliasManagerInterface $alias_manager, ContextManager $context_manager) {
    $this->aliasManager = $alias_manager;
    $this->contextManager = $context_manager;
  }

  /**
   *
   */
  public function resolveUrl($path) {
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
