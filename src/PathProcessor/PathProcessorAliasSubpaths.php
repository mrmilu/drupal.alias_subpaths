<?php

namespace Drupal\alias_subpaths\PathProcessor;

use Drupal\alias_subpaths\AliasSubpathsUrlResolver;
use Drupal\alias_subpaths\ContextManager;
use Drupal\Core\PathProcessor\InboundPathProcessorInterface;
use Drupal\path_alias\AliasManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Processes the inbound path using path alias lookups.
 */
class PathProcessorAliasSubpaths implements InboundPathProcessorInterface {

  /**
   * @var \Drupal\alias_subpaths\AliasSubpathsUrlResolver
   */
  private AliasSubpathsUrlResolver $aliasSubpathsUrlResolver;

  /**
   * @param \Drupal\alias_subpaths\AliasSubpathsUrlResolver $alias_subpaths_url_resolver
   */
  public function __construct(AliasSubpathsUrlResolver $alias_subpaths_url_resolver) {
    $this->aliasSubpathsUrlResolver = $alias_subpaths_url_resolver;
  }

  /**
   * {@inheritdoc}
   */
  public function processInbound($path, Request $request) {
    return $this->aliasSubpathsUrlResolver->resolveUrl($path);
  }

}
