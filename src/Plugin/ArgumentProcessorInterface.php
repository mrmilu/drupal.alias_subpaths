<?php

namespace Drupal\alias_subpaths\Plugin;

interface ArgumentProcessorInterface {
  public function process($context_argument, $allowed_argument_types);
}
