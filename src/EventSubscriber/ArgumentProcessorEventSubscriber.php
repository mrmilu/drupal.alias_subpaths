<?php

namespace Drupal\alias_subpaths\EventSubscriber;

use Drupal\alias_subpaths\AliasSubpathsManager;
use Drupal\alias_subpaths\Exception\InvalidArgumentException;
use Drupal\alias_subpaths\Exception\NotAllowedArgumentsException;
use Drupal\Core\Cache\CacheableResponseInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Routing\AdminContext;
use Drupal\Core\Routing\CurrentRouteMatch;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Processes alias subpaths arguments on kernel requests.
 *
 * This subscriber validates and processes alias subpaths parameters for routes,
 * adjusting the current route options accordingly.
 */
class ArgumentProcessorEventSubscriber implements EventSubscriberInterface {

  /**
   * The current route match service.
   *
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
   * The alias subpaths manager service.
   *
   * @var \Drupal\alias_subpaths\AliasSubpathsManager
   */
  private AliasSubpathsManager $aliasSubpathsManager;

  /**
   * The module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  private ModuleHandlerInterface $moduleHandler;

  /**
   * Constructs a new ArgumentProcessorEventSubscriber.
   *
   * @param \Drupal\Core\Routing\CurrentRouteMatch $current_route_match
   *   The current route match service.
   * @param \Drupal\alias_subpaths\AliasSubpathsManager $alias_subpaths_manager
   *   The alias subpaths manager service.
   * @param \Drupal\Core\Routing\AdminContext $admin_context
   *   The admin context service.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler service.
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
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = ['onRequest', 31];
    $events[KernelEvents::RESPONSE][] = ['onResponse'];
    return $events;
  }

  /**
   * Processes the request event to handle alias subpaths resolution.
   *
   * This method checks whether the current route is applicable for alias
   * subpaths processing. If it is, the alias subpaths are resolved; otherwise,
   * the request is skipped. If resolution fails, a NotFoundHttpException is
   * thrown.
   *
   * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
   *   The request event.
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

    // Add new parameter to current route to determine if the route is a route
    // that we are validating with this module.
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
   * Determines if the current route is a system route.
   *
   * @return bool
   *   TRUE if the current route is a system route, FALSE otherwise.
   */
  private function isSystemRoute() {
    $route_name = $this->currentRouteMatch->getRouteName();
    return str_starts_with($route_name, "system.");
  }

  /**
   * Determines if the current route is a views route.
   *
   * @return bool
   *   TRUE if the current route is a views route, FALSE otherwise.
   */
  private function isViewsRoute() {
    $route_name = $this->currentRouteMatch->getRouteName();
    return str_starts_with($route_name, "view.");
  }

  /**
   * Determines if the current request corresponds to the front page.
   *
   * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
   *   The request event.
   *
   * @return bool
   *   TRUE if the request path is '/', FALSE otherwise.
   */
  private function isFrontPage(RequestEvent $event) {
    return $event->getRequest()->getPathInfo() === '/';
  }

  /**
   * Determines if the current route is an admin route.
   *
   * @return bool
   *   TRUE if the current route is an admin route, FALSE otherwise.
   */
  public function isAdminRoute() {
    return $this->adminContext->isAdminRoute($this->currentRouteMatch->getRouteObject());
  }

  /**
   * Checks if the current route is a media library route.
   *
   * This is a special case because the media library uses a custom route that
   * does not belong to admin routes.
   *
   * @return bool
   *   TRUE if it's a media library route, FALSE otherwise.
   */
  public function isMediaLibraryRoute() {
    $route_object = $this->currentRouteMatch->getRouteObject();
    return str_starts_with($route_object->getPath(), '/media-library');
  }

  /**
   * Determines if the current route is the decoupled router path translation.
   *
   * @return bool
   *   TRUE if the current route is the decoupled router path translation route,
   *   FALSE otherwise.
   */
  private function isDecoupledRouterRoute() {
    $route_name = $this->currentRouteMatch->getRouteName();
    return $route_name === "decoupled_router.path_translation";
  }


  public function onResponse(ResponseEvent $event) {
    if ($this->isSystemRoute() ||
      $this->isAdminRoute() ||
      ($event->getRequest()->getPathInfo() === '/') ||
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
    try {
      $path_data = $this->aliasSubpathsManager->resolve($requested_uri);
    }
    catch (NotAllowedArgumentsException | InvalidArgumentException $exception) {
      throw new NotFoundHttpException();
    }

    $response = $event->getResponse();

    foreach ($path_data['params'] as $param) {
      if (is_array($param)) {
        foreach ($param as $arg) {
          $this->addCacheableDependency($response, $arg);
        }
      } else {
        $this->addCacheableDependency($response, $param);
      }
    }
  }

  private function addCacheableDependency(Response $response, mixed $param) {
    if (!$param instanceof EntityInterface || !$response instanceof CacheableResponseInterface) {
      return;
    }
    $response->addCacheableDependency($param);
  }

}
