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
    $request = $event->getRequest();
    if ($request->attributes->get('_disable_alias_subpaths')) {
      return;
    }

    $requested_uri = urldecode($request->getPathInfo());

    // Add new parameter to current route to determine if the route is a route
    // that we are validating with this module.
    $this->currentRouteMatch->getRouteObject()->setOption('_alias_subpaths_route', TRUE);

    // Disable route normalizer to avoid 301.
    if ($this->moduleHandler->moduleExists('redirect')) {
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
   * Processes the response event to add cacheable dependencies.
   *
   * This method is invoked during the response event. It checks if alias
   * subpaths processing is disabled for the current request by inspecting the
   * '_disable_alias_subpaths' attribute. If not disabled, it decodes the
   * requested URI and attempts to resolve alias subpaths via the
   * AliasSubpathsManager. The resolved data contains parameters,
   * which are then iterated. For each parameter (or each value in a parameter
   * array), if it represents an entity, its cacheable dependency is added to
   * the response.
   *
   * This ensures that if any of these entities are modified, the page cache
   * will be invalidated.
   *
   * @param \Symfony\Component\HttpKernel\Event\ResponseEvent $event
   *   The response event containing the request and response objects.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
   *   Thrown if resolution fails due to invalid or not allowed arguments.
   */
  public function onResponse(ResponseEvent $event) {
    $request = $event->getRequest();
    if ($request->attributes->get('_disable_alias_subpaths')) {
      return;
    }

    $requested_uri = urldecode($request->getPathInfo());

    $params = $this->aliasSubpathsManager->getContextManager()->getContextBag($requested_uri)->getProcessedContent();

    $response = $event->getResponse();

    foreach ($params as $param) {
      if (is_array($param)) {
        foreach ($param as $arg) {
          $this->addCacheableDependency($response, $arg);
        }
      }
      else {
        $this->addCacheableDependency($response, $param);
      }
    }
  }

  /**
   * Adds the given parameter as a cacheable dependency to the response.
   *
   * If the parameter is an instance of EntityInterface and the response
   * implements CacheableResponseInterface, the entity is added as a
   * cacheable dependency to the response. This ensures that changes to
   * the entity will trigger invalidation of the page cache.
   *
   * @param \Symfony\Component\HttpFoundation\Response $response
   *   The response object to which the cacheable dependency should be added.
   * @param mixed $param
   *   The parameter to evaluate.
   */
  private function addCacheableDependency(Response $response, mixed $param) {
    if (!$param instanceof EntityInterface || !$response instanceof CacheableResponseInterface) {
      return;
    }
    $response->addCacheableDependency($param);
  }

}
