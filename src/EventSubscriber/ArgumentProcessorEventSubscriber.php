<?php

namespace Drupal\alias_subpaths\EventSubscriber;

use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\alias_subpaths\Plugin\ArgumentProcessorInterface;
use Drupal\alias_subpaths\Plugin\ArgumentProcessorManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
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

  public function __construct(
    ArgumentProcessorManager $argument_processor_manager,
    CurrentRouteMatch $current_route_match
  ) {
    $this->argumentProcessorManager = $argument_processor_manager;
    $this->currentRouteMatch = $current_route_match;
  }

  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = ['onRequest', 0];
    return $events;
  }

  public function onRequest(RequestEvent $event) {
    $route_name = $this->currentRouteMatch->getRouteName();
    foreach ($this->argumentProcessorManager->getDefinitions() as $definition) {
      if ($definition['route_name'] === $route_name) {
        $plugin = $this->argumentProcessorManager->createInstance($definition['id']);
        if ($plugin->hasArguments()) {
          $plugin->process();
        }
        return;
      }
    }
  }

}
