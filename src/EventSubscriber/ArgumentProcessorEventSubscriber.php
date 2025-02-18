<?php

namespace Drupal\alias_subpaths\EventSubscriber;

use Drupal\alias_subpaths\AliasSubpathsManager;
use Drupal\alias_subpaths\ContextManager;
use Drupal\alias_subpaths\Exception\InvalidArgumentException;
use Drupal\alias_subpaths\Exception\NotAllowedArgumentsException;
use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Core\Routing\AdminContext;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\alias_subpaths\Plugin\ArgumentProcessorManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ArgumentProcessorEventSubscriber implements EventSubscriberInterface {

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
   * @var \Drupal\alias_subpaths\AliasSubpathsManager
   */
  private AliasSubpathsManager $aliasSubpathsManager;

  /**
   * @param \Drupal\Core\Routing\CurrentRouteMatch $current_route_match
   * @param \Drupal\alias_subpaths\AliasSubpathsManager $alias_subpaths_manager
   * @param \Drupal\Core\Routing\AdminContext $admin_context
   */
  public function __construct(
    CurrentRouteMatch $current_route_match,
    AliasSubpathsManager $alias_subpaths_manager,
    AdminContext $admin_context
  ) {
    $this->currentRouteMatch = $current_route_match;
    $this->adminContext = $admin_context;
    $this->aliasSubpathsManager = $alias_subpaths_manager;
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
      $this->isMediaLibraryRoute()
    ) {
      return;
    }

    $requested_uri = urldecode($event->getRequest()->getPathInfo());

    // Add new parameter to current route to determine if the route is a route that we are validating with this module.
    $this->currentRouteMatch->getRouteObject()->setOption('_alias_subpaths_route', TRUE);

    try {
      $this->aliasSubpathsManager->resolve($requested_uri);
    } catch (NotAllowedArgumentsException|InvalidArgumentException $exception) {
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

  /**
   * Check if we are in a media library rout, this is a special case because the
   * media library it uses a custom route that does not belong to admin routes.
   *
   * @return bool
   */
  public function isMediaLibraryRoute(){
    $route_object = $this->currentRouteMatch->getRouteObject();
    return str_starts_with($route_object->getPath(), '/media-library');
  }

}
