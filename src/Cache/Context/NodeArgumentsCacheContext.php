<?php

namespace Drupal\path_alias_arg\Cache\Context;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Cache\Context\CacheContextInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class NodeArgumentsCacheContext implements CacheContextInterface {

  protected $requestStack;

  public function __construct(RequestStack $request_stack) {
    $this->requestStack = $request_stack;
  }

  public static function getLabel() {
    return t('Node URL Arguments');
  }

  public function getContext() {
    $request = $this->requestStack->getCurrentRequest();
    $parameters = trim($request->getPathInfo(), '/');
    return md5($parameters);
  }

  public function getCacheTags() {
    return [];
  }

  public function getCacheableMetadata() {
    return new CacheableMetadata();
  }

}
