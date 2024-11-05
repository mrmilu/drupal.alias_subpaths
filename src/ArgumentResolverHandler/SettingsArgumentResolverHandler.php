<?php

namespace Drupal\alias_subpaths\ArgumentResolverHandler;

use Drupal\alias_subpaths\ArgumentResolver\ArgumentResolverInterface;
use Drupal\alias_subpaths\ArgumentResolver\DefaultArgumentResolver;
use Drupal\Core\Site\Settings;

class SettingsArgumentResolverHandler implements ArgumentResolverHandlerInterface {

  public function getAllowedArgumentTypes($id) {
    $allowed_arguments_definitions = Settings::get('alias_subpaths__allowed_arguments_definitions');
    if (!array_key_exists($id, $allowed_arguments_definitions)) {
      return FALSE;
    }
    return $allowed_arguments_definitions[$id];
  }

  public function getArgumentResolver($id): ArgumentResolverInterface {
    $argument_resolver_classes = Settings::get('alias_subpaths__argument_resolver_class');
    if (!array_key_exists($id, $argument_resolver_classes)) {
      return new DefaultArgumentResolver();
    }
    $resolver_class = $argument_resolver_classes[$id];
    return new $resolver_class();
  }

}
