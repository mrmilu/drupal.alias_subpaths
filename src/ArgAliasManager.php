<?php

namespace Drupal\path_alias_arg;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\path_alias\AliasManager;
use Drupal\path_alias\AliasRepositoryInterface;
use Drupal\path_alias\AliasWhitelistInterface;

class ArgAliasManager extends AliasManager {

  public function __construct(AliasRepositoryInterface $alias_repository, AliasWhitelistInterface $whitelist, LanguageManagerInterface $language_manager, CacheBackendInterface $cache, ?TimeInterface $time = NULL) {
    parent::__construct($alias_repository, $whitelist, $language_manager, $cache);
    $this->time = $time;
  }

  public function getPathByAlias($path, $langcode = NULL) {
    return $this->getPathAndArgumentsByAlias($path, $langcode)['alias'];
  }

  public function getPathArgumentsByAlias($path, $langcode = NULL) {
    return $this->getPathAndArgumentsByAlias($path, $langcode)['arguments'];
  }

  public function getPathAndArgumentsByAlias($path, $langcode = NULL) {
    $arguments = [];
    $path_parts = explode('/', trim($path, '/'));
    while (count($path_parts) > 0) {
      $current_path = '/' . implode('/', $path_parts);
      $alias = parent::getPathByAlias($current_path, $langcode);
      if ($alias !== $current_path) {
        return ['alias' => $alias, 'arguments' => $arguments];
      }
      $argument = array_pop($path_parts);
      array_unshift($arguments, $argument);
    }
    return ['alias' => $path, 'arguments' => $arguments];
  }

}
