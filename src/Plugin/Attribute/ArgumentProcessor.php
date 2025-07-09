<?php

namespace Drupal\alias_subpaths\Plugin\Attribute;

use Drupal\Component\Plugin\Attribute\Plugin;

/**
 * Defines the ArgumentProcessor attribute.
 *
 * This attribute is used to mark classes as argument processors
 * for specific routes.
 *
 * @see \Drupal\Component\Plugin\Attribute\Plugin
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class ArgumentProcessor extends Plugin {

  /**
   * Constructs a new ArgumentProcessor attribute.
   *
   * @param string $id
   *   The identifier for the argument processor.
   * @param string $route_name
   *   The route name that this argument processor applies to.
   */
  public function __construct(
    public readonly string $id,
    public readonly string $route_name,
  ) {}

}
