<?php

namespace Drupal\alias_subpaths\ArgumentResolver;

interface ArgumentResolverInterface {

  /**
   * @param $value
   *  The argument input value.
   *
   * @return mixed
   *  The processed value or NULL if not possible to process.
   */
  public function resolve($value);

}
