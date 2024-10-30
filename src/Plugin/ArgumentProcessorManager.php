<?php

namespace Drupal\path_alias_arg\Plugin;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\path_alias_arg\Plugin\Attribute\ArgumentProcessor;

class ArgumentProcessorManager extends DefaultPluginManager {

  public function __construct(\Traversable $namespaces,
                              CacheBackendInterface $cache_backend,
                              ModuleHandlerInterface $module_handler
  ) {
    parent::__construct(
      'Plugin/ArgumentProcessor',
      $namespaces,
      $module_handler,
      'Drupal\path_alias_arg\Plugin\ArgumentProcessorInterface',
      ArgumentProcessor::class
    );
    $this->alterInfo('argument_processor_info');
    $this->setCacheBackend($cache_backend, 'argument_processor_info_plugins');
  }

}
