<?php

namespace Drupal\alias_subpaths;

use Drupal\alias_subpaths\Plugin\ArgumentProcessorManager;

class ContextBag {

  /**
   * Array to hold the raw content.
   *
   * @var \Drupal\alias_subpaths\ContextParam[]
   */
  protected array $params;

  /**
   * Array to hold the processed content.
   *
   * @var array
   */
  protected array $processedContent;

  /**
   * @var \Drupal\alias_subpaths\Plugin\ArgumentProcessorManager
   */
  private ArgumentProcessorManager $argumentProcessorManager;

  /**
   * Internal drupal path of route
   *
   * @var string|null
   */
  private ?string $path = NULL;

  private ?array $routeInfo = NULL;

  /**
   * Constructs a new ContextBagFactory object.
   *
   * @param \Drupal\alias_subpaths\Plugin\ArgumentProcessorManager $argumentProcessorManager
   *   The ArgumentProcessorManager service.
   */
  public function __construct(ArgumentProcessorManager $argumentProcessorManager) {
    $this->argumentProcessorManager = $argumentProcessorManager;
    $this->processedContent = [];
    $this->params = [];
  }

  public function add($raw_content): void {
    $this->params[] = new ContextParam($raw_content);
  }

  public function addProcessed($key, $processed_content): void {
    $this->processedContent[$key] = $processed_content;
  }

  /**
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  public function process(): array {
    $route_name = $this->routeInfo['name'];
    foreach ($this->argumentProcessorManager->getDefinitions() as $definition) {
      if ($definition['route_name'] === $route_name) {
        /** @var \Drupal\alias_subpaths\Plugin\ArgumentProcessorInterface $plugin */
        $plugin = $this->argumentProcessorManager->createInstance($definition['id']);
        $plugin->run($this);
        return $this->processedContent;
      }
    }
    return [];
  }

  /**
   * @return bool
   */
  public function isEmpty(): bool {
    return empty($this->params);
  }

  /**
   * @return \Drupal\alias_subpaths\ContextParam[]
   */
  public function getParams(): array {
    return $this->params;
  }

  public function getProcessedContent() {
    return $this->processedContent;
  }

  /**
   * @return string|null
   */
  public function getPath(): ?string {
    return $this->path;
  }

  /**
   * @param string $alias
   *
   * @return string
   */
  public function setPath(string $alias): string {
    $this->path = $alias;
    return $this->path;
  }

  public function getRouteInfo(): ?array {
    return $this->routeInfo;
  }
  public function setRouteInfo(array $route_info): ?array {
    $this->routeInfo = $route_info;
    return $this->routeInfo;
  }

}
