<?php

namespace Drupal\alias_subpaths\EventSubscriber;

use Drupal\alias_subpaths\AliasSubpathsManager;
use Drupal\alias_subpaths\Exception\InvalidArgumentException;
use Drupal\alias_subpaths\Exception\NotAllowedArgumentsException;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Routing\AdminContext;
use Drupal\Core\Routing\CurrentRouteMatch;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 *
 */
class ArgumentProcessorEventSubscriber implements EventSubscriberInterface {

  /**
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  private CurrentRouteMatch $currentRouteMatch;

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
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  private ModuleHandlerInterface $moduleHandler;

  /**
   * @param \Drupal\Core\Routing\CurrentRouteMatch $current_route_match
   * @param \Drupal\alias_subpaths\AliasSubpathsManager $alias_subpaths_manager
   * @param \Drupal\Core\Routing\AdminContext $admin_context
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   */
  public function __construct(
    CurrentRouteMatch $current_route_match,
    AliasSubpathsManager $alias_subpaths_manager,
    AdminContext $admin_context,
    ModuleHandlerInterface $module_handler,
  ) {
    $this->currentRouteMatch = $current_route_match;
    $this->adminContext = $admin_context;
    $this->aliasSubpathsManager = $alias_subpaths_manager;
    $this->moduleHandler = $module_handler;
  }

  /**
   *
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = ['onRequest', 31];
    return $events;
  }

  /**
   *
   */
  public function onRequest(RequestEvent $event) {
    if ($this->isSystemRoute() ||
      $this->isAdminRoute() ||
      $this->isFrontPage($event) ||
      $this->isViewsRoute() ||
      $this->isMediaLibraryRoute()
    ) {
      return;
    }

    if ($this->moduleHandler->moduleExists('decoupled_router') &&
      $this->isDecoupledRouterRoute()
    ) {
      return;
    }

    $requested_uri = urldecode($event->getRequest()->getPathInfo());

    // Add new parameter to current route to determine if the route is a route that we are validating with this module.
    $this->currentRouteMatch->getRouteObject()->setOption('_alias_subpaths_route', TRUE);

    // Disable route normalizer to avoid 301.
    if ($this->moduleHandler->moduleExists('redirect')) {
      $request = $event->getRequest();
      $request->attributes->set('_disable_route_normalizer', TRUE);
    }

    try {
      $this->aliasSubpathsManager->resolve($requested_uri);
    }
    catch (NotAllowedArgumentsException | InvalidArgumentException $exception) {
      throw new NotFoundHttpException();
    }
  }

  /**
   *
   */
  private function isSystemRoute() {
    $route_name = $this->currentRouteMatch->getRouteName();
    return str_starts_with($route_name, "system.");
  }

  /**
   *
   */
  private function isViewsRoute() {
    $route_name = $this->currentRouteMatch->getRouteName();
    return str_starts_with($route_name, "view.");
  }

  /**
   *
   */
  private function isFrontPage(RequestEvent $event) {
    return $event->getRequest()->getPathInfo() === '/';
  }

  /**
   *
   */
  public function isAdminRoute() {
    return $this->adminContext->isAdminRoute($this->currentRouteMatch->getRouteObject());
  }

  /**
   * Check if we are in a media library rout, this is a special case because the
   * media library it uses a custom route that does not belong to admin routes.
   *
   * @return bool
   */
  public function isMediaLibraryRoute() {
    $route_object = $this->currentRouteMatch->getRouteObject();
    return str_starts_with($route_object->getPath(), '/media-library');
  }

  /**
   *
   */
  private function isDecoupledRouterRoute() {
    $route_name = $this->currentRouteMatch->getRouteName();
    return $route_name === "decoupled_router.path_translation";
  }

}
