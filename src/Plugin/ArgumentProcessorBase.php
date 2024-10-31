<?php

namespace Drupal\alias_subpaths\Plugin;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\alias_subpaths\ContextManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ArgumentProcessorBase extends PluginBase implements ArgumentProcessorInterface, ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\alias_subpaths\ContextManager
   */
  protected ContextManager $contextManager;

  /**
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected CurrentRouteMatch $currentRouteMatch;

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    ContextManager $context_manager,
    CurrentRouteMatch $current_route_match
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->contextManager = $context_manager;
    $this->currentRouteMatch = $current_route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('alias_subpaths.context_manager'),
      $container->get('current_route_match')
    );
  }

  public function process() {
    //@TODO: Implement process() method.
  }

  public function hasArguments() {
    return TRUE;
  }

}
