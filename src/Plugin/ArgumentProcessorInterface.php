<?php

namespace Drupal\alias_subpaths\Plugin;

use Drupal\alias_subpaths\ContextParam;

/**
 * Defines an interface for processing alias subpaths arguments.
 *
 * This interface specifies methods for executing and processing alias subpaths
 * arguments, allowing implementations to define custom logic for handling
 * context-specific parameters.
 */
interface ArgumentProcessorInterface {

  /**
   * Executes the argument processing routine.
   *
   * This method triggers the processing logic for alias subpaths arguments.
   */
  public function run(): void;

  /**
   * Processes the provided context argument based on allowed argument types.
   *
   * @param \Drupal\alias_subpaths\ContextParam $context_argument
   *   The context parameter containing the alias subpaths argument.
   * @param mixed $allowed_argument_types
   *   The allowed types for the argument.
   *
   * @return mixed
   *   The result of processing the context argument.
   */
  public function process(ContextParam $context_argument, $allowed_argument_types);

}
