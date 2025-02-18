<?php

namespace Drupal\alias_subpaths_decoupled_router\EventSubscriber;

use Drupal\alias_subpaths\AliasSubpathsAliasManager;
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
   * @var \Drupal\alias_subpaths\AliasSubpathsAliasManager
   */
  private AliasSubpathsAliasManager $aliasSubpathsAliasManager;

  /**
   * @var \Symfony\Component\Routing\RouterInterface
   */
  private RouterInterface $router;

  /**
   * @var \Drupal\alias_subpaths\ContextManager
   */
  private ContextManager $contextManager;

  /**
   * @param \Drupal\alias_subpaths\AliasSubpathsAliasManager $alias_subpaths_url_resolver
   * @param \Symfony\Component\Routing\RouterInterface $router
   * @param \Drupal\alias_subpaths\ContextManager $context_manager
   */
  public function __construct(
    AliasSubpathsAliasManager $alias_subpaths_url_resolver,
    RouterInterface $router,
    ContextManager $context_manager
  ) {
    $this->aliasSubpathsAliasManager = $alias_subpaths_url_resolver;
    $this->router = $router;
    $this->contextManager = $context_manager;
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
    $internal_path = $this->aliasSubpathsAliasManager->resolveUrl($path);
    try {
      $route_parameters = $this->router->match($internal_path);
    } catch (MethodNotAllowedException|NoConfigurationException|ResourceNotFoundException $e) {
      return;
    }

    if (empty($route_parameters['_route'])) {
      return;
    }

    $route_name = $route_parameters['_route'];
    try {
      $this->contextManager->processContextBag($path);
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
