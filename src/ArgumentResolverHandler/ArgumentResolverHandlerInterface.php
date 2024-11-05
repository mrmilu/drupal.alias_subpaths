<?php

namespace Drupal\alias_subpaths\ArgumentResolverHandler;

use Drupal\alias_subpaths\ArgumentResolver\ArgumentResolverInterface;

interface ArgumentResolverHandlerInterface {
  public function getAllowedArgumentTypes($id) ;
  public function getArgumentResolver($id): ArgumentResolverInterface;
}
