<?php

namespace Drupal\alias_subpaths;

use Drupal\alias_subpaths\Plugin\ArgumentProcessorManager;

/**
 * Represents a bag of context parameters for alias subpaths.
 *
 * This class holds both raw and processed context parameters extracted from
 * a given path. It also stores additional information such as the resolved
 * internal path and route information. The context bag is processed using the
 * configured argument processor plugins.
 */
class ContextBag {

  /**
   * An array holding the raw context parameters.
   *
   * Each element is an instance of \Drupal\alias_subpaths\ContextParam.
   *
   * @var \Drupal\alias_subpaths\ContextParam[]
   */
  protected array $params;

  /**
   * An array holding the processed context content.
   *
   * This array is generated after processing the raw context parameters.
   *
   * @var array
   */
  protected array $processedContent;

  /**
   * The ArgumentProcessorManager service.
   *
   * @var \Drupal\alias_subpaths\Plugin\ArgumentProcessorManager
   */
  private ArgumentProcessorManager $argumentProcessorManager;

  /**
   * The internal Drupal path of the matched route.
   *
   * @var string|null
   */
  private ?string $path = NULL;

  /**
   * The route information associated with the current context.
   *
   * This is typically an associative array containing route details.
   *
   * @var array|null
   */
  private ?array $routeInfo = NULL;

  /**
   * Constructs a new ContextBag object.
   *
   * @param \Drupal\alias_subpaths\Plugin\ArgumentProcessorManager $argumentProcessorManager
   *   The ArgumentProcessorManager service.
   */
  public function __construct(ArgumentProcessorManager $argumentProcessorManager) {
    $this->argumentProcessorManager = $argumentProcessorManager;
    $this->processedContent = [];
    $this->params = [];
  }

  /**
   * Adds raw content to the context bag.
   *
   * The raw content is wrapped in a ContextParam object and stored in the bag.
   *
   * @param mixed $raw_content
   *   The raw content to add.
   */
  public function add($raw_content): void {
    $this->params[$raw_content] = new ContextParam($raw_content);
  }

  /**
   * Processes the context bag using the configured argument processors.
   *
   * It iterates through the available argument processor definitions to
   * find one that matches the current route name. The corresponding
   * processor plugin is then instantiated and executed, generating
   * the processed content.
   *
   * @return array
   *   An associative array of processed context content.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   *   If an error occurs during plugin processing.
   */
  public function process(): array {
    $route_name = $this->routeInfo['name'];
    foreach ($this->argumentProcessorManager->getDefinitions() as $definition) {
      if ($definition['route_name'] === $route_name) {
        /** @var \Drupal\alias_subpaths\Plugin\ArgumentProcessorInterface $plugin */
        $plugin = $this->argumentProcessorManager->createInstance($definition['id'], ['context_bag' => $this]);
        $plugin->run();
        return $this->generateProcessedContent();
      }
    }
    return [];
  }

  /**
   * Determines whether the context bag is empty.
   *
   * @return bool
   *   TRUE if there are no raw context parameters, FALSE otherwise.
   */
  public function isEmpty(): bool {
    return empty($this->params);
  }

  /**
   * Retrieves the raw context parameters.
   *
   * @return \Drupal\alias_subpaths\ContextParam[]
   *   An array of raw context parameters.
   */
  public function getParams(): array {
    return $this->params;
  }

  /**
   * Gets the processed context content.
   *
   * @return array
   *   An associative array of processed context content.
   */
  public function getProcessedContent() {
    return $this->processedContent;
  }

  /**
   * Retrieves the processed value for a specific parameter.
   *
   * @param string $param
   *   The parameter key.
   *
   * @return mixed
   *   The processed value for the parameter, or NULL if not found.
   */
  public function getProcessedValue($param) {
    if (array_key_exists($param, $this->processedContent)) {
      return $this->processedContent[$param];
    }
    return NULL;
  }

  /**
   * Generates the processed content from the raw context parameters.
   *
   * This method aggregates the processed values from each parameter using their
   * parameter names as keys.
   *
   * @return array
   *   An associative array of processed context content.
   */
  private function generateProcessedContent(): array {
    $processedContent = array_reduce($this->params, function (array $processedContent, $param): array {
      $key = $param->getParamName();
      if ($key === NULL) {
        return $processedContent;
      }
      $value = $param->getProcessedValue();
      if (!array_key_exists($key, $processedContent)) {
        $processedContent[$key] = $value;
      }
      else {
        $processedContent[$key] = is_array($processedContent[$key])
          ? array_merge($processedContent[$key], [$value])
          : [$processedContent[$key], $value];
      }
      return $processedContent;
    }, []);
    $this->processedContent = $processedContent;
    return $this->processedContent;
  }

  /**
   * Retrieves the internal Drupal path associated with the context.
   *
   * @return string|null
   *   The internal path if set, or NULL otherwise.
   */
  public function getPath(): ?string {
    return $this->path;
  }

  /**
   * Sets the internal Drupal path for the context.
   *
   * @param string $alias
   *   The resolved internal path.
   *
   * @return string
   *   The internal path that was set.
   */
  public function setPath(string $alias): string {
    $this->path = $alias;
    return $this->path;
  }

  /**
   * Retrieves the route information for the context.
   *
   * @return array|null
   *   An associative array containing route information, or NULL if not set.
   */
  public function getRouteInfo(): ?array {
    return $this->routeInfo;
  }

  /**
   * Sets the route information for the context.
   *
   * @param array $route_info
   *   An associative array of route information.
   *
   * @return array|null
   *   The route information that was set.
   */
  public function setRouteInfo(array $route_info): ?array {
    $this->routeInfo = $route_info;
    return $this->routeInfo;
  }

}
