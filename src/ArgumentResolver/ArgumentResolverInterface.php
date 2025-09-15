<?php

namespace Drupal\alias_subpaths\ArgumentResolver;

/**
 * Defines an interface for argument resolvers.
 *
 * This interface specifies methods that must be implemented by classes
 * responsible for validating, processing, and providing default values for
 * alias subpaths arguments.
 */
interface ArgumentResolverInterface {

  /**
   * Gets the parameter name associated with the argument.
   *
   * @return string
   *   The parameter name.
   */
  public function getParamName(): string;

  /**
   * Validates the provided argument value.
   *
   * @param mixed $value
   *   The argument input value.
   *
   * @return bool
   *   TRUE if the argument value is valid, FALSE otherwise.
   */
  public function resolve($value): bool;

  /**
   * Gets the default value for the argument.
   *
   * @return mixed
   *   The default value.
   */
  public function getDefaultValue(): mixed;

  /**
   * Processes the provided argument value.
   *
   * @param mixed $value
   *   The input argument value.
   *
   * @return mixed
   *   The processed value.
   */
  public function getProcessedValue($value): mixed;

}
