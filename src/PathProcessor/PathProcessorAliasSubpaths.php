<?php

namespace Drupal\alias_subpaths\PathProcessor;

use Drupal\alias_subpaths\AliasSubpathsAliasManager;
use Drupal\alias_subpaths\ContextManager;
use Drupal\Core\PathProcessor\InboundPathProcessorInterface;
use Drupal\path_alias\AliasManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Processes the inbound path using path alias lookups.
 */
class PathProcessorAliasSubpaths implements InboundPathProcessorInterface {

  /**
   * @var \Drupal\alias_subpaths\AliasSubpathsAliasManager
   */
  private AliasSubpathsAliasManager $aliasSubpathsAliasManager;

  /**
   * @param \Drupal\alias_subpaths\AliasSubpathsAliasManager $alias_subpaths_url_resolver
   */
  public function __construct(AliasSubpathsAliasManager $alias_subpaths_url_resolver) {
    $this->aliasSubpathsAliasManager = $alias_subpaths_url_resolver;
  }

  /**
   * {@inheritdoc}
   */
  public function processInbound($path, Request $request) {
    return $this->aliasSubpathsAliasManager->resolveUrl($path);

    //$pathData = $this->aliasSubpathsManager->resolve($path);
    //return $pathData['path'];
  }

}
