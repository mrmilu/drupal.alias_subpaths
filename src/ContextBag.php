<?php

namespace Drupal\alias_subpaths;

use Drupal\alias_subpaths\Plugin\ArgumentProcessorManager;

class ContextBag {

  /**
   * Array to hold the raw content.
   *
   * @var array
   */
  protected array $rawContent;

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
   * Constructs a new ContextBagFactory object.
   *
   * @param \Drupal\alias_subpaths\Plugin\ArgumentProcessorManager $argumentProcessorManager
   *   The ArgumentProcessorManager service.
   */
  public function __construct(ArgumentProcessorManager $argumentProcessorManager) {
    $this->argumentProcessorManager = $argumentProcessorManager;
    $this->processedContent = [];
    $this->rawContent = [];
  }

  public function add($raw_content): void {
    $this->rawContent[] = $raw_content;
  }

  public function addProcessed($key, $processed_content): void {
    $this->processedContent[$key] = $processed_content;
  }

  /**
   * @param $route_name
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  public function process($route_name): array {
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
    return empty($this->rawContent);
  }

  /**
   * @return array
   */
  public function getRawContent(): array {
    return $this->rawContent;
  }

}
