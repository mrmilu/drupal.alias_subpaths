<?php

namespace Drupal\alias_subpaths\Plugin;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\alias_subpaths\Plugin\Attribute\ArgumentProcessor;

/**
 * Manages the discovery and instantiation of argument processor plugins.
 *
 * This plugin manager is responsible for loading, instantiating, and managing
 * plugins that implement the ArgumentProcessorInterface. It uses the
 * "ArgumentProcessor" attribute to discover available plugins.
 */
class ArgumentProcessorManager extends DefaultPluginManager {

  /**
   * Constructs a new ArgumentProcessorManager.
   *
   * @param \Traversable $namespaces
   *   An object containing the root paths keyed by the corresponding namespace
   *   where plugin implementations can be found.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   The cache backend used for storing plugin definitions.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler service for managing module-specific alterations.
   */
  public function __construct(
    \Traversable $namespaces,
    CacheBackendInterface $cache_backend,
    ModuleHandlerInterface $module_handler,
  ) {
    parent::__construct(
      'Plugin/ArgumentProcessor',
      $namespaces,
      $module_handler,
      'Drupal\alias_subpaths\Plugin\ArgumentProcessorInterface',
      ArgumentProcessor::class
    );
    $this->alterInfo('argument_processor_info');
    $this->setCacheBackend($cache_backend, 'argument_processor_info_plugins');
  }

}
