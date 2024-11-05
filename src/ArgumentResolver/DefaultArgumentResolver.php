<?php

namespace Drupal\alias_subpaths\ArgumentResolver;

class DefaultArgumentResolver implements ArgumentResolverInterface {

  public function resolve($value) {
    return $value;
  }

}
