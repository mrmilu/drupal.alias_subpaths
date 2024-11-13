<?php

namespace Drupal\alias_subpaths\EventSubscriber;

use Drupal\alias_subpaths\ContextManager;
use Drupal\alias_subpaths\Exception\InvalidArgumentException;
use Drupal\alias_subpaths\Exception\NotAllowedArgumentsException;
use Drupal\Core\Routing\AdminContext;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\alias_subpaths\Plugin\ArgumentProcessorManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ArgumentProcessorEventSubscriber implements EventSubscriberInterface {

  /**
   * @var \Drupal\alias_subpaths\Plugin\ArgumentProcessorManager
   */
  private ArgumentProcessorManager $argumentProcessorManager;

  /**
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  private CurrentRouteMatch $currentRouteMatch;

  /**
   * @var \Drupal\alias_subpaths\ContextManager
   */
  private ContextManager $contextManager;

  /**
   * The admin context service.
   *
   * @var \Drupal\Core\Routing\AdminContext
   */
  protected $adminContext;

  /**
   * @param \Drupal\alias_subpaths\Plugin\ArgumentProcessorManager $argument_processor_manager
   * @param \Drupal\Core\Routing\CurrentRouteMatch $current_route_match
   * @param \Drupal\alias_subpaths\ContextManager $context_manager
   */
  public function __construct(
    ArgumentProcessorManager $argument_processor_manager,
    CurrentRouteMatch $current_route_match,
    ContextManager $context_manager,
    AdminContext $admin_context
  ) {
    $this->argumentProcessorManager = $argument_processor_manager;
    $this->currentRouteMatch = $current_route_match;
    $this->contextManager = $context_manager;
    $this->adminContext = $admin_context;
  }

  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = ['onRequest', 0];
    return $events;
  }

  public function onRequest(RequestEvent $event) {
    if ($this->isSystemRoute() ||
      $this->isAdminRoute() ||
      $this->isFrontPage($event) ||
      $this->isViewsRoute() ||
      $this->contextManager->contextBagIsEmpty()
    ) {
      return;
    }
    $requestedUri = $event->getRequest()->getPathInfo();
    $route_name = $this->currentRouteMatch->getRouteName();
    foreach ($this->argumentProcessorManager->getDefinitions() as $definition) {
      if ($definition['route_name'] === $route_name) {
        /** @var \Drupal\alias_subpaths\Plugin\ArgumentProcessorInterface $plugin */
        $plugin = $this->argumentProcessorManager->createInstance($definition['id']);
        try {
          $plugin->run();
        } catch (NotAllowedArgumentsException|InvalidArgumentException $exception) {
          throw new NotFoundHttpException();
        }
        return;
      }
    }
    if ($requestedUri !== $this->contextManager->getResolvedUrl()) {
      throw new NotFoundHttpException();
    }
  }

  private function isSystemRoute() {
    $route_name = $this->currentRouteMatch->getRouteName();
    return str_starts_with($route_name, "system.");
  }

  private function isViewsRoute() {
    $route_name = $this->currentRouteMatch->getRouteName();
    return str_starts_with($route_name, "view.");
  }

  private function isFrontPage(RequestEvent $event) {
    return $event->getRequest()->getPathInfo() === '/';
  }

  public function isAdminRoute() {
    return $this->adminContext->isAdminRoute($this->currentRouteMatch->getRouteObject());
  }

}
