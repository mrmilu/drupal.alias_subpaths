<?php

namespace Drupal\alias_subpaths;

use Drupal\alias_subpaths\Plugin\ArgumentProcessorManager;

/**
 * Class ContextBagFactory.
 *
 * Factory class for creating instances of ContextBag with the required dependencies.
 */
class ContextBagFactory {

  /**
   * The ArgumentProcessorManager service.
   *
   * @var \Drupal\alias_subpaths\Plugin\ArgumentProcessorManager
   */
  protected ArgumentProcessorManager $argumentProcessorManager;

  /**
   * Constructs a new ContextBagFactory object.
   *
   * @param \Drupal\alias_subpaths\Plugin\ArgumentProcessorManager $argumentProcessorManager
   *   The ArgumentProcessorManager service.
   */
  public function __construct(ArgumentProcessorManager $argumentProcessorManager) {
    $this->argumentProcessorManager = $argumentProcessorManager;
  }

  /**
   * Creates and returns a new instance of ContextBag.
   *
   * @return \Drupal\alias_subpaths\ContextBag
   *   A new instance of ContextBag.
   */
  public function create(): ContextBag {
    return new ContextBag($this->argumentProcessorManager);
  }
}
