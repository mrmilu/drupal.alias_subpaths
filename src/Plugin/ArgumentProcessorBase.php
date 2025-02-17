<?php

namespace Drupal\alias_subpaths\Plugin;

use Drupal\alias_subpaths\ArgumentResolverHandler\ArgumentResolverHandlerInterface;
use Drupal\alias_subpaths\ContextBag;
use Drupal\alias_subpaths\Exception\InvalidArgumentException;
use Drupal\alias_subpaths\Exception\NotAllowedArgumentsException;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\alias_subpaths\ContextManager;
use Drupal\Core\Site\Settings;
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

  protected ArgumentResolverHandlerInterface $handler;

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
    $handlerClass = $this->getHandlerClass();
    $this->handler = new $handlerClass();
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

  public function process($context_argument, $allowed_argument_types) {
    foreach ($allowed_argument_types as $argument_type) {
      $argument_resolver =  $this->handler->getArgumentResolver($argument_type);
      if (!$argument_resolver->resolve($context_argument)) {
        continue;
      }
      return [
        $argument_resolver->getParamName() => $argument_resolver->getProcessedValue($context_argument)
      ];
    }
    throw new InvalidArgumentException();
  }

  public function getAllowedArgumentTypes() {
    return $this->handler->getAllowedArgumentTypes($this->getId());
  }

  public function run(ContextBag $contextBag) {
    if (!$allowed_argument_types = $this->getAllowedArgumentTypes()) {
      throw new NotAllowedArgumentsException();
    }

    foreach ($contextBag->getParams() as $idx => $context_argument) {
      $processed_argument = $this->process($context_argument->getRawValue(), $allowed_argument_types);

      $contextBag->addProcessed($idx, $processed_argument);
    }
  }

  /**
   * @return mixed
   */
  protected function getHandlerClass(): mixed {
    return Settings::get(
      'alias_subpaths__argument_resolver_handler_class',
      '\Drupal\alias_subpaths\ArgumentResolverHandler\SettingsArgumentResolverHandler'
    );
  }

  protected function getId() {
    return $this->getPluginId();
  }

}
