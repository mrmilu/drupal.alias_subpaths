<?php

namespace Drupal\alias_subpaths;

class ContextManager {

  /**
   * Array to hold the context.
   *
   * @var array
   */
  protected $contextBag;

  /**
   * @var \Drupal\alias_subpaths\ContextBagFactory
   */
  private ContextBagFactory $contextBagFactory;

  /**
   * Constructor for ContextManager.
   */
  public function __construct(ContextBagFactory $contextBagFactory) {
    $this->contextBag = [];
    $this->contextBagFactory = $contextBagFactory;
  }

  /**
   * Initializes a ContextBag for a given key.
   *
   * @param mixed $key
   *   The key to associate with the ContextBag.
   *
   * @return \Drupal\alias_subpaths\ContextBag
   *   The initialized ContextBag.
   */
  public function initContextBag($key): ContextBag {
    $this->contextBag[$key] = $this->contextBagFactory->create();
    return $this->contextBag[$key];
  }

  /**
   * @param $key
   *
   * @return \Drupal\alias_subpaths\ContextBag|null
   */
  public function getContextBag($key): ?ContextBag {
    return $this->contextBag[$key] ?? NULL;
  }

  /**
   * @param $key
   * @param $route_name
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  public function processContextBag($key, $route_name): array {
    return $this->getContextBag($key)->process($route_name);
  }

  public function isEmpty(string $key): bool {
    return (!$this->getContextBag($key) || $this->getContextBag($key)->isEmpty());
  }

}
