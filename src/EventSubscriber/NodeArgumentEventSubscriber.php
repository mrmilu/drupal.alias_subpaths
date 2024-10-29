<?php

namespace Drupal\path_alias_arg\EventSubscriber;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Path\CurrentPathStack;
use Drupal\path_alias\AliasManagerInterface;
use Drupal\path_alias_arg\PathAliasArgStorage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Drupal\node\Entity\Node;

class NodeArgumentEventSubscriber implements EventSubscriberInterface {

  /**
   * The alias manager that caches alias lookups based on the request.
   *
   * @var \Drupal\path_alias\AliasManagerInterface
   */
  protected $aliasManager;

  /**
   * The current path.
   *
   * @var \Drupal\Core\Path\CurrentPathStack
   */
  protected $currentPath;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  private EntityTypeManagerInterface $entityTypeManager;

  /**
   * @var \Drupal\path_alias_arg\PathAliasArgStorage
   */
  private PathAliasArgStorage $path_alias_arg_storage;

  /**
   * Constructs a new PathSubscriber instance.
   *
   * @param \Drupal\path_alias\AliasManagerInterface $alias_manager
   *   The alias manager.
   * @param \Drupal\Core\Path\CurrentPathStack $current_path
   *   The current path.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\path_alias_arg\PathAliasArgStorage $path_alias_arg_storage
   *   The path alias Storage handler.
   */
  public function __construct(AliasManagerInterface $alias_manager,
                              CurrentPathStack $current_path,
                              EntityTypeManagerInterface $entityTypeManager,
                              PathAliasArgStorage $path_alias_arg_storage
  ) {
    $this->aliasManager = $alias_manager;
    $this->currentPath = $current_path;
    $this->entityTypeManager = $entityTypeManager;
    $this->path_alias_arg_storage = $path_alias_arg_storage;
  }

  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = ['onRequest', 0];
    return $events;
  }

  public function onRequest(RequestEvent $event) {
    if (!$event->isMainRequest()) {
      return;
    }
    $request = $event->getRequest();

    if ($request->attributes->get('_route') !== "entity.node.canonical") {
      return;
    }

    $alias = $request->getPathInfo();
    $path = $this->currentPath->getPath($request);
    if ($alias === $path) {
      return;
    }

    $node = $this->loadNodeByPath($path);

    if ($node instanceof Node) {

      $config = \Drupal::config('path_alias_arg.settings');
      $allowed_argument_types = $config->get($node->bundle() . '__selected_entity_bundles');
      if (!$allowed_argument_types) {
        return;
      }
      $arguments = [];

      foreach ($this->aliasManager->getPathArgumentsByAlias($alias) as $argument) {
        if (($processed_argument = $this->processArgument($argument, $allowed_argument_types)) === NULL ) {
          throw new NotFoundHttpException();
        }
        $processed_argument_key = $this->getProcessedArgumentKey($processed_argument);
        if (array_key_exists($processed_argument_key, $arguments)) {
          throw new NotFoundHttpException();
        }
        $arguments[$processed_argument_key] = $processed_argument;
      }
      $request->attributes->set('path_alias_arguments', $arguments);
    }
  }

  private function loadNodeByPath($alias) {
    $path = $this->aliasManager->getPathByAlias($alias);
    if (preg_match('/node\/(\d+)/', $path, $matches)) {
      return $this->entityTypeManager->getStorage('node')->load($matches[1]);
    }
    return null;
  }

  private function processArgument(mixed $argument, $allowed_argument_types) {
    foreach ($allowed_argument_types as $entity_type_bundle) {
      [$entity_type, $bundle] = explode('__', $entity_type_bundle);
      if ($entity = $this->loadEntityByEncodedTitle($entity_type, $bundle, $argument)) {
        return $entity;
      }
    }
    return NULL;
  }

  function loadEntityByEncodedTitle($entity_type, $bundle, $string) {
    if ($urlencoded_key_entity_data = $this->path_alias_arg_storage->getByUrlencodedKeyAndEntityType($string, $entity_type)) {
      $entities = $this->entityTypeManager->getStorage($entity_type)->loadByProperties(['uuid' => $urlencoded_key_entity_data["entity_uuid"]]);
      return reset($entities);
    }

    return NULL;
  }

  private function getProcessedArgumentKey(?\Drupal\Core\Entity\EntityInterface $processed_argument) {
    return  join('--', [$processed_argument->getEntityTypeId(), $processed_argument->id()]);
  }

}
