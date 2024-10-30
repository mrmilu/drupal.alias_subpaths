<?php

namespace Drupal\path_alias_arg\EventSubscriber;

use Drupal\path_alias_arg\Plugin\ArgumentProcessorManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ArgumentProcessorEventSubscriber implements EventSubscriberInterface {

  /**
   * @var \Drupal\path_alias_arg\Plugin\ArgumentProcessorManager
   */
  private ArgumentProcessorManager $argumentProcessorManager;

  public function __construct(ArgumentProcessorManager $argument_processor_manager) {
    $this->argumentProcessorManager = $argument_processor_manager;
  }

  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = ['onRequest', 0];
    return $events;
  }

  public function onRequest(RequestEvent $event) {
    $route_name = $event->getRequest()->attributes->get('_route');
    foreach ($this->argumentProcessorManager->getDefinitions() as $definition) {
      if ($definition['route_name'] === $route_name) {
        $plugin = $this->argumentProcessorManager->createInstance($definition['id']);
        $plugin->process();
        break;
      }
    }
  }

}
