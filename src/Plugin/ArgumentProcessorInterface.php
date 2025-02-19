<?php

namespace Drupal\alias_subpaths\Plugin;

use Drupal\alias_subpaths\ContextParam;

/**
 *
 */
interface ArgumentProcessorInterface {

  /**
   * @return void
   */
  public function run(): void;

  /**
   *
   */
  public function process(ContextParam $context_argument, $allowed_argument_types);

}
