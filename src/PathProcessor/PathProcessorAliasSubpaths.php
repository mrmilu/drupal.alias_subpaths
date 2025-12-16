<?php

namespace Drupal\alias_subpaths\PathProcessor;

use Drupal\alias_subpaths\AliasSubpathsAliasManager;
use Drupal\alias_subpaths\Exception\NotRouteApplicableException;
use Drupal\Core\PathProcessor\InboundPathProcessorInterface;
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
   * Constructs a new PathProcessorAliasSubpaths.
   *
   * @param \Drupal\alias_subpaths\AliasSubpathsAliasManager $alias_subpaths_url_resolver
   *   The alias subpaths alias manager service.
   */
  public function __construct(AliasSubpathsAliasManager $alias_subpaths_url_resolver) {
    $this->aliasSubpathsAliasManager = $alias_subpaths_url_resolver;
  }

  /**
   * {@inheritdoc}
   */
  public function processInbound($path, Request $request) {
    if ($request->attributes->get('_disable_alias_subpaths')) {
      return $path;
    }

    if (\Drupal::moduleHandler()->moduleExists('redirect')) {
      $redirectRespository = \Drupal::service('redirect.repository');
      $sourcePath = trim($path, '/');
      $redirects = $redirectRespository->findBySourcePath($sourcePath);

      $redirect = reset($redirects);
      if ($redirect) {
        return $path;
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
