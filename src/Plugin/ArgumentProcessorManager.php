<?php

namespace Drupal\alias_subpaths\Plugin;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\alias_subpaths\Plugin\Attribute\ArgumentProcessor;

/**
 *
 */
class ArgumentProcessorManager extends DefaultPluginManager {

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
