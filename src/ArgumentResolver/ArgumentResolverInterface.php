<?php

namespace Drupal\alias_subpaths\ArgumentResolver;

/**
 *
 */
interface ArgumentResolverInterface {

  /**
   *
   */
  public function getParamName(): string;

  /**
   * @param $value
   *   The argument input value.
   *
   * @return bool
   *   Returns if argument is valid.
   */
  public function resolve($value): bool;

  /**
   * @return mixed
   *   Returns default value.
   */
  public function getDefaultValue(): mixed;

  /**
   * @param $value
   *
   * @return mixed
   *   Returns processed value.
   */
  public function getProcessedValue($value): mixed;

}
