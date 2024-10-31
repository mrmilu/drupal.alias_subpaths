<?php

namespace Drupal\alias_subpaths;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Modifica el contenedor de servicios.
 */
class AliasSubpathsServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    if ($container->hasDefinition('path_alias.manager')) {
      $definition = $container->getDefinition('path_alias.manager');
      $definition->setClass('\Drupal\alias_subpaths\AliasSubpathsAliasManager')
        ->addArgument(
          new Reference('alias_subpaths.context_manager')
        );
    }
  }

}
