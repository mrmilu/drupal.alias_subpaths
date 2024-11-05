<?php

namespace Drupal\alias_subpaths\Exception;

use Drupal\Component\Plugin\Exception\PluginException;

/**
 * Defines a custom exception for invalid arguments.
 */
class InvalidArgumentException extends PluginException {

  /**
   * Constructs a new InvalidArgumentException.
   *
   * @param string $message
   *   (optional) The exception message to throw.
   * @param int $code
   *   (optional) The exception code.
   * @param \Throwable|null $previous
   *   (optional) The previous throwable used for the exception chaining.
   */
  public function __construct($message = "Invalid argument.", $code = 0, \Throwable $previous = NULL) {
    parent::__construct($message, $code, $previous);
  }

}
