<?php

namespace Drupal\alias_subpaths\PathProcessor;

use Drupal\alias_subpaths\ContextManager;
use Drupal\Core\PathProcessor\InboundPathProcessorInterface;
use Drupal\path_alias\AliasManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Processes the inbound path using path alias lookups.
 */
class PathProcessorAliasSubpaths implements InboundPathProcessorInterface {

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
   * {@inheritdoc}
   */
  public function processInbound($path, Request $request) {
    $this->contextManager->initContextBag($path);
    $path_parts = explode('/', trim($path, '/'));

    while (count($path_parts) > 0) {
      $current_alias = '/' . implode('/', $path_parts);
      $current_path = $this->aliasManager->getPathByAlias($current_alias);
      if ($current_path !== $current_alias) {
        return $current_path;
      }
      $argument = array_pop($path_parts);
      $this->contextManager->getContextBag($path)->add($argument);
    }

    return $path;
  }

}
