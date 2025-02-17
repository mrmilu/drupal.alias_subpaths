<?php

namespace Drupal\alias_subpaths;

class ContextManager {

  /**
   * Array to hold the context.
   *
   * @var array
   */
  protected array $contextBag;

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
   * @param $key
   *
   * @return \Drupal\alias_subpaths\ContextBag
   */
  public function getContextBag($key): ContextBag {
    if (array_key_exists($key, $this->contextBag)) {
      return $this->contextBag[$key];
    }
    $this->contextBag[$key] = $this->contextBagFactory->create();
    return $this->contextBag[$key];
  }

  /**
   * @param $key
   * @param $route_name
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  public function processContextBag($key, $route_name): array {
    return $this->getContextBag($key)->process();
  }

  public function isEmpty(string $key): bool {
    return (!$this->getContextBag($key) || $this->getContextBag($key)->isEmpty());
  }

}
