<?php

namespace Drupal\alias_subpaths\ArgumentResolver;

/**
 * Base implementation of the ArgumentResolverInterface.
 *
 * Provides default behaviors for resolving, processing, and retrieving default
 * values for alias subpaths arguments.
 */
class BaseArgumentResolver implements ArgumentResolverInterface {

  const PARAM_NAME = 'argument';

  /**
   * {@inheritdoc}
   */
  public function getParamName(): string {
    return static::PARAM_NAME;
  }

  /**
   * {@inheritdoc}
   */
  public function resolve($value): bool {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultValue(): mixed {
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getProcessedValue($value): mixed {
    return $value;
  }

}
