<?php

namespace Drupal\alias_subpaths\Exception;

use Drupal\Component\Plugin\Exception\PluginException;

/**
 * Defines a custom exception for invalid arguments.
 */
class InvalidArgumentException extends PluginException {

  /**
   * Constructs a new InvalidArgumentException.
   */
  public function __construct() {
    $message = "Invalid argument.";
    parent::__construct($message);
  }

}
