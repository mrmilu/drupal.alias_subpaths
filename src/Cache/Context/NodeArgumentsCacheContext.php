<?php

namespace Drupal\alias_subpaths\Cache\Context;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Cache\Context\CacheContextInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Provides a cache context based on node URL arguments.
 *
 * This cache context generates a unique identifier based on the URL
 * arguments of the current request, which is useful for caching node-related
 * pages with dynamic parameters.
 */
class NodeArgumentsCacheContext implements CacheContextInterface {

  /**
   * The request stack service.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * Constructs a new NodeArgumentsCacheContext.
   *
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack service.
   */
  public function __construct(RequestStack $request_stack) {
    $this->requestStack = $request_stack;
  }

  /**
   * Gets the label for this cache context.
   *
   * @return string
   *   A translated label for the cache context.
   */
  public static function getLabel() {
    return t('Node URL Arguments');
  }

  /**
   * {@inheritdoc}
   */
  public function getContext() {
    $request = $this->requestStack->getCurrentRequest();
    $parameters = trim($request->getPathInfo(), '/');
    return md5($parameters);
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheableMetadata() {
    return new CacheableMetadata();
  }

}
