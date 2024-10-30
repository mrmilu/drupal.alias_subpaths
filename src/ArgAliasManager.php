<?php

namespace Drupal\path_alias_arg;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\path_alias\AliasManager;
use Drupal\path_alias\AliasRepositoryInterface;
use Drupal\path_alias\AliasWhitelistInterface;

class ArgAliasManager extends AliasManager {

  /**
   * @var \Drupal\path_alias_arg\ContextManager
   */
  private ContextManager $context_manager;

  /**
   * @param \Drupal\path_alias\AliasRepositoryInterface $alias_repository
   * @param \Drupal\path_alias\AliasWhitelistInterface $whitelist
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   * @param \Drupal\path_alias_arg\ContextManager $context_manager
   */
  public function __construct(
    AliasRepositoryInterface $alias_repository,
    AliasWhitelistInterface $whitelist,
    LanguageManagerInterface $language_manager,
    CacheBackendInterface $cache,
    ContextManager $context_manager,
  ) {
    parent::__construct($alias_repository, $whitelist, $language_manager, $cache);
    $this->context_manager = $context_manager;
  }

  public function getPathByAlias($path, $langcode = NULL) {
    $path_parts = explode('/', trim($path, '/'));
    while (count($path_parts) > 0) {
      $current_path = '/' . implode('/', $path_parts);
      $alias = parent::getPathByAlias($current_path, $langcode);
      if ($alias !== $current_path) {
        return $alias;
      }
      $argument = array_pop($path_parts);
      $this->context_manager->addToContextBag($argument);
    }
    return $path;
  }

}
