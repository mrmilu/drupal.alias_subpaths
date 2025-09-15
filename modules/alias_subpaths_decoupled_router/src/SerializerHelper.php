<?php

namespace Drupal\alias_subpaths_decoupled_router;

use Drupal\Core\Entity\EntityInterface;

/**
 * Provides helper methods for serializing arguments.
 *
 * This class offers a static method to convert various types of arguments
 * into an array representation. If the argument is a Drupal entity (i.e.,
 * it implements EntityInterface), it will be serialized into an associative
 * array that includes the entity's canonical URL, type, bundle, ID, and UUID.
 * For non-entity arguments, the method returns an array containing the
 * JSON-encoded value.
 *
 * @package Drupal\alias_subpaths_decoupled_router
 */
class SerializerHelper {

  /**
   * Serializes an argument into an array representation.
   *
   * If the argument is an instance of EntityInterface, an associative array
   * with the entity's canonical URL, type, bundle, ID, and UUID is returned.
   * Otherwise, the argument is JSON-encoded and returned as a single-element
   * array.
   *
   * @param mixed $argument
   *   The argument to serialize. Typically, this is an entity or any other
   *   serializable value.
   *
   * @return array
   *   An array representation of the serialized argument.
   *   - For entities, the array contains:
   *     - 'canonical': The absolute canonical URL of the entity.
   *     - 'type': The entity type ID.
   *     - 'bundle': The entity bundle.
   *     - 'id': The entity ID.
   *     - 'uuid': The entity UUID.
   *   - For non-entity arguments, the array contains a single element with the
   *     JSON-encoded value.
   *
   * @throws \Drupal\Core\Entity\EntityMalformedException
   *   Thrown if the entity is malformed.
   */
  public static function serialize(mixed $argument): array {
    if ($argument instanceof EntityInterface) {
      $serialized_argument = [
        'canonical' => $argument->toUrl('canonical', ['absolute' => TRUE])
          ->toString(),
        'type' => $argument->getEntityTypeId(),
        'bundle' => $argument->bundle(),
        'id' => $argument->id(),
        'uuid' => $argument->uuid(),
      ];
    }
    else {
      $serialized_argument = [
        // @todo work on this response
        json_encode($argument),
      ];
    }
    return $serialized_argument;
  }

}
