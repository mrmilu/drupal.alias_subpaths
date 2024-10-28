<?php

namespace Drupal\path_alias_arg;

use Drupal\Core\Database\Connection;
use Drupal\Core\Database\DatabaseException;

/**
 * Class PathAliasArgStorage.
 * Provides a handler for the path_alias_arg_node_key_map table.
 */
class PathAliasArgStorage {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Constructs a new PathAliasArgStorage object.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * Retrieves a record by entity UUID.
   *
   * @param string $entity_uuid
   *   The entity UUID.
   *
   * @return array|false
   *   The record as an associative array, or FALSE if not found.
   */
  public function getByEntityUuid(string $entity_uuid) {
    try {
      $query = $this->database->select('path_alias_arg_node_key_map', 'p')
        ->fields('p')
        ->condition('entity_uuid', $entity_uuid)
        ->execute();
      return $query->fetchAssoc();
    }
    catch (DatabaseException $e) {
      return FALSE;
    }
  }

  /**
   * Retrieves a record by entity url encoded key.
   *
   * @param string $urlencoded_key
   *   The entity urlencoded key.
   * @param string $entity_type
   *
   * @return array|false
   *   The record as an associative array, or FALSE if not found.
   * @throws \Exception
   */
  public function getByUrlencodedKeyAndEntityType(string $urlencoded_key, string $entity_type) {
    try {
      $query = $this->database->select('path_alias_arg_node_key_map', 'p')
        ->fields('p')
        ->condition('urlencoded_key', $urlencoded_key)
        ->condition('entity_type', $entity_type)
        ->execute();
      return $query->fetchAssoc();
    }
    catch (DatabaseException $e) {
      return FALSE;
    }
  }

  /**
   * Inserts a new record.
   *
   * @param string $entity_uuid
   *   The entity UUID.
   * @param string $urlencoded_key
   *   The URL encoded key.
   * @param string $entity_type
   *
   * @return bool
   *   TRUE if the record was inserted, FALSE otherwise.
   * @throws \Exception
   */
  public function insertRecord(string $entity_uuid, string $urlencoded_key, string $entity_type): bool {
    try {
      $this->database->insert('path_alias_arg_node_key_map')
        ->fields([
          'entity_uuid' => $entity_uuid,
          'urlencoded_key' => $urlencoded_key,
          'entity_type' => $entity_type,
        ])
        ->execute();
      return TRUE;
    }
    catch (DatabaseException $e) {
      return FALSE;
    }
  }

  /**
   * Updates an existing record by entity UUID.
   *
   * @param string $entity_uuid
   *   The entity UUID.
   * @param string $urlencoded_key
   *   The new URL encoded key.
   *
   * @return bool
   *   TRUE if the record was updated, FALSE otherwise.
   */
  public function updateRecord(string $entity_uuid, string $urlencoded_key): bool {
    try {
      $count = $this->database->update('path_alias_arg_node_key_map')
        ->fields(['urlencoded_key' => $urlencoded_key])
        ->condition('entity_uuid', $entity_uuid)
        ->execute();
      return $count > 0;
    }
    catch (DatabaseException $e) {
      return FALSE;
    }
  }
}
