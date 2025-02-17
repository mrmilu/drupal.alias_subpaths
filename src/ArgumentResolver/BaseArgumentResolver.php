<?php

namespace Drupal\alias_subpaths\ArgumentResolver;

class BaseArgumentResolver implements ArgumentResolverInterface {

  const PARAM_NAME = 'argument';

  public function getParamName(): string {
    return static::PARAM_NAME;
  }

  public function resolve($value): bool {
    return TRUE;
  }

  public function getDefaultValue(): mixed {
    return NULL;
  }

  public function getProcessedValue($value): mixed {
    return $value;
  }

}
