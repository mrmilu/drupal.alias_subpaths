<?php

namespace Drupal\alias_subpaths\Plugin\Attribute;

use Attribute;
use Drupal\Component\Plugin\Attribute\Plugin;

#[Attribute(Attribute::TARGET_CLASS)]
class ArgumentProcessor extends Plugin {
  public function __construct(
    public readonly string $id,
    public readonly string $route_name,
  ) {}
}
