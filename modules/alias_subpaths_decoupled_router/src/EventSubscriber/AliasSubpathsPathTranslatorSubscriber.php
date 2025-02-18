<?php

namespace Drupal\alias_subpaths_decoupled_router\EventSubscriber;

use Drupal\alias_subpaths\AliasSubpathsAliasManager;
use Drupal\alias_subpaths\AliasSubpathsManager;
use Drupal\alias_subpaths\ContextManager;
use Drupal\alias_subpaths\Exception\InvalidArgumentException;
use Drupal\alias_subpaths\Exception\NotAllowedArgumentsException;
use Drupal\Core\Cache\CacheableJsonResponse;
use Drupal\decoupled_router\PathTranslatorEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\NoConfigurationException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RouterInterface;

class AliasSubpathsPathTranslatorSubscriber implements EventSubscriberInterface {

  /**
   * @var \Drupal\alias_subpaths\AliasSubpathsManager
   */
  private AliasSubpathsManager $aliasSubpathsManager;

  /**
   * @param \Drupal\alias_subpaths\AliasSubpathsManager $alias_subpaths_manager
   */
  public function __construct(
    AliasSubpathsManager $alias_subpaths_manager,
  ) {
    $this->aliasSubpathsManager = $alias_subpaths_manager;
  }

  public static function getSubscribedEvents() {
      $events[PathTranslatorEvent::TRANSLATE][] = ['onPathTranslation'];
    return $events;
  }

  /**
   * Processes a path translation request.
   */
  public function onPathTranslation(PathTranslatorEvent $event) {
    $path = $event->getPath();
    try {
      $this->aliasSubpathsManager->resolve($path);
    } catch (NotAllowedArgumentsException|InvalidArgumentException $exception) {
      $event->getResponse()->setData([
        'message' => t(
          'Unable to resolve path @path.',
          ['@path' => $path]
        ),
        'details' => t(
          'None of the available methods were able to find a match for this path.'
        ),
      ]);
      $event->getResponse()->setStatusCode(404);
      $event->stopPropagation();
    }

  }

}
