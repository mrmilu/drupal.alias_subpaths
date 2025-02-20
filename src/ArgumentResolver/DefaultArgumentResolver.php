<?php

namespace Drupal\alias_subpaths\ArgumentResolver;

/**
 * Provides a default implementation of an argument resolver.
 *
 * This class extends BaseArgumentResolver and defines the default parameter
 * name used when no specific resolver is configured.
 *
 * {@inheritdoc}
 */
class DefaultArgumentResolver extends BaseArgumentResolver {
  const PARAM_NAME = 'default';

}
