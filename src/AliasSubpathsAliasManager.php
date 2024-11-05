<?php

namespace Drupal\alias_subpaths;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\path_alias\AliasManager;
use Drupal\path_alias\AliasRepositoryInterface;
use Drupal\path_alias\AliasWhitelistInterface;

class AliasSubpathsAliasManager extends AliasManager {

  /**
   * @var \Drupal\alias_subpaths\ContextManager
   */
  private ContextManager $context_manager;

  /**
   * @param \Drupal\path_alias\AliasRepositoryInterface $alias_repository
   * @param \Drupal\path_alias\AliasWhitelistInterface $whitelist
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   * @param \Drupal\Component\Datetime\TimeInterface $time
   * @param \Drupal\alias_subpaths\ContextManager $context_manager
   */
  public function __construct(
    AliasRepositoryInterface $alias_repository,
    AliasWhitelistInterface $whitelist,
    LanguageManagerInterface $language_manager,
    CacheBackendInterface $cache,
    TimeInterface $time,
    ContextManager $context_manager,
  ) {
    parent::__construct($alias_repository, $whitelist, $language_manager, $cache, $time);
    $this->context_manager = $context_manager;
  }

  public function getPathByAlias($alias, $langcode = NULL) {
    $this->context_manager->setRequestedUrl($alias);
    $alias_parts= explode('/', trim($alias, '/'));
    while (count($alias_parts) > 0) {
      $current_alias = '/' . implode('/', $alias_parts);
      $path = parent::getPathByAlias($current_alias, $langcode);
      if ($path !== $current_alias) {
        $this->context_manager->setResolvedUrl($current_alias);
        return $path;
      }
      $argument = array_pop($alias_parts);
      $this->context_manager->addToContextBag($argument);
    }
    $this->context_manager->setResolvedUrl($alias);
    return $alias;
  }

}
