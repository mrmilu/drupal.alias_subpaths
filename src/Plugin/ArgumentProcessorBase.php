<?php

namespace Drupal\alias_subpaths\Plugin;

use Drupal\alias_subpaths\ArgumentResolverHandler\ArgumentResolverHandlerInterface;
use Drupal\alias_subpaths\ContextBag;
use Drupal\alias_subpaths\ContextParam;
use Drupal\alias_subpaths\Exception\InvalidArgumentException;
use Drupal\alias_subpaths\Exception\NotAllowedArgumentsException;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\alias_subpaths\ContextManager;
use Drupal\Core\Site\Settings;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a base implementation for argument processor plugins.
 *
 * This class serves as the base for plugins that process alias subpaths
 * arguments. It handles common dependency injection and processing logic,
 * including resolving the appropriate argument resolver and applying the
 * processing to the context parameters.
 */
class ArgumentProcessorBase extends PluginBase implements ArgumentProcessorInterface, ContainerFactoryPluginInterface {

  /**
   * The context manager service.
   *
   * @var \Drupal\alias_subpaths\ContextManager
   */
  protected ContextManager $contextManager;

  /**
   * The current route match service.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected CurrentRouteMatch $currentRouteMatch;

  /**
   * The argument resolver handler.
   *
   * @var \Drupal\alias_subpaths\ArgumentResolverHandler\ArgumentResolverHandlerInterface
   */
  protected ArgumentResolverHandlerInterface $handler;

  /**
   * The context bag containing context parameters.
   *
   * @var \Drupal\alias_subpaths\ContextBag
   */
  protected ContextBag $contextBag;

  /**
   * Constructs a new ArgumentProcessorBase.
   *
   * @param array $configuration
   *   An array of configuration values.
   * @param string $plugin_id
   *   The plugin identifier.
   * @param mixed $plugin_definition
   *   The plugin definition.
   * @param \Drupal\alias_subpaths\ContextManager $context_manager
   *   The context manager service.
   * @param \Drupal\Core\Routing\CurrentRouteMatch $current_route_match
   *   The current route match service.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    ContextManager $context_manager,
    CurrentRouteMatch $current_route_match,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->contextManager = $context_manager;
    $this->currentRouteMatch = $current_route_match;
    $handlerClass = $this->getHandlerClass();
    $this->handler = new $handlerClass();
    $this->contextBag = $configuration['context_bag'];
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

  /**
   * Processes the provided context argument with allowed argument types.
   *
   * Iterates through each allowed argument type and uses the corresponding
   * argument resolver to attempt to resolve the raw value from the context
   * argument. On a successful resolution, the context argument is updated with
   * the processed value and parameter name.
   *
   * @param \Drupal\alias_subpaths\ContextParam $context_argument
   *   The context parameter to process.
   * @param mixed $allowed_argument_types
   *   The allowed types for the argument.
   *
   * @return bool
   *   TRUE if processing succeeds.
   *
   * @throws \Drupal\alias_subpaths\Exception\InvalidArgumentException
   *   Thrown when none of the allowed argument types resolve the argument.
   */
  public function process(ContextParam $context_argument, $allowed_argument_types) {
    foreach ($allowed_argument_types as $argument_type) {
      $argument_resolver = $this->handler->getArgumentResolver($argument_type);
      $raw_value = $context_argument->getRawValue();
      if (!$argument_resolver->resolve($raw_value)) {
        continue;
      }
      $context_argument->setParamName($argument_resolver->getParamName());
      $context_argument->setProcessedValue($argument_resolver->getProcessedValue($raw_value));
      return TRUE;
    }
    throw new InvalidArgumentException();
  }

  /**
   * Retrieves the allowed argument types for this plugin.
   *
   * @return mixed
   *   The allowed argument types.
   */
  public function getAllowedArgumentTypes() {
    return $this->handler->getAllowedArgumentTypes($this->getId());
  }

  /**
   * Executes the argument processing routine.
   *
   * If the route does not allow arguments and the context bag is empty, no
   * processing is performed. Otherwise, the allowed argument types are
   * retrieve and each context parameter is processed accordingly.
   *
   * @throws \Drupal\alias_subpaths\Exception\NotAllowedArgumentsException
   *   Thrown when the route does not allow arguments.
   */
  public function run(): void {
    if (!$this->routeAllowArguments() && $this->contextBag->isEmpty()) {
      return;
    }
    if (!$allowed_argument_types = $this->getAllowedArgumentTypes()) {
      throw new NotAllowedArgumentsException();
    }

    foreach ($this->contextBag->getParams() as $context_argument) {
      $this->process($context_argument, $allowed_argument_types);
    }
  }

  /**
   * Retrieves the handler class used for argument resolution.
   *
   * @return mixed
   *   The fully qualified class name of the argument resolver handler.
   */
  protected function getHandlerClass(): mixed {
    return Settings::get(
      'alias_subpaths__argument_resolver_handler_class',
      '\Drupal\alias_subpaths\ArgumentResolverHandler\SettingsArgumentResolverHandler'
    );
  }

  /**
   * Gets the plugin identifier.
   *
   * @return string
   *   The unique identifier for this plugin.
   */
  protected function getId() {
    return $this->getPluginId();
  }

  /**
   * Checks whether the current route allows arguments for this plugin.
   *
   * @return bool
   *   TRUE if the route allows arguments, FALSE otherwise.
   */
  private function routeAllowArguments() {
    return $this->handler->routeAllowArguments($this->getId());
  }

}
