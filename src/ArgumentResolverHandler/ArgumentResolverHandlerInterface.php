<?php

namespace Drupal\alias_subpaths\ArgumentResolverHandler;

use Drupal\alias_subpaths\ArgumentResolver\ArgumentResolverInterface;

/**
 * Defines an interface for handling argument resolvers.
 *
 * This interface provides methods to determine if a route associated with a
 * given plugin identifier allows arguments, to retrieve the allowed argument
 * types, and to obtain the corresponding argument resolver.
 */
interface ArgumentResolverHandlerInterface {

  /**
   * Determines whether the route for the given plugin ID allows arguments.
   *
   * @param string $id
   *   The plugin identifier.
   *
   * @return bool
   *   TRUE if the route allows arguments, FALSE otherwise.
   */
  public function routeAllowArguments($id);

  /**
   * Retrieves the allowed argument types for the specified plugin ID.
   *
   * @param string $id
   *   The plugin identifier.
   *
   * @return mixed
   *   The allowed argument types if defined, or FALSE if not configured.
   */
  public function getAllowedArgumentTypes($id);

  /**
   * Retrieves the argument resolver for the specified plugin ID.
   *
   * If no specific argument resolver is configured for the given ID, a default
   * argument resolver should be returned.
   *
   * @param string $id
   *   The plugin identifier.
   *
   * @return \Drupal\alias_subpaths\ArgumentResolver\ArgumentResolverInterface
   *   The argument resolver instance.
   */
  public function getArgumentResolver($id): ArgumentResolverInterface;

}
