<?php

namespace Drupal\alias_subpaths\Exception;

use Drupal\Component\Plugin\Exception\PluginException;

/**
 * Defines a custom exception for not allowed arguments.
 */
class NotAllowedArgumentsException extends PluginException {

  /**
   * Constructs a new NotAllowedArgumentsException.
   */
  public function __construct() {
    $message = "Arguments are not allowed.";
    parent::__construct($message);
  }

}
