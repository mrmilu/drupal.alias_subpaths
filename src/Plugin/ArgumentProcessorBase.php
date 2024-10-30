<?php

namespace Drupal\path_alias_arg\Plugin;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\path_alias_arg\ContextManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ArgumentProcessorBase extends PluginBase implements ArgumentProcessorInterface, ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\path_alias_arg\ContextManager
   */
  protected ContextManager $contextManager;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, ContextManager $context_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->contextManager = $context_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('path_alias_arg.context_manager')
    );
  }

  public function process() {
    // TODO: Implement process() method.
  }

}
