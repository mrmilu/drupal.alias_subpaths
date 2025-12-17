<?php

namespace Drupal\alias_subpaths\Exception;

use Drupal\Component\Plugin\Exception\PluginException;

/**
 * Defines a custom exception for non-applicable routes.
 */
class NotRouteApplicableException extends PluginException {

  /**
   * Constructs a new NotRouteApplicableException.
   */
  public function __construct() {
    $message = "Route not applicable.";
    parent::__construct($message);
  }

}
