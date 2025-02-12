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
   * @deprecated
   *
   * Array to hold the processed context.
   *
   * @var array
   */
  protected $processedContextBag;

  private string $requestedUrl;

  private string $resolvedUrl;

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
    $this->processedContextBag = [];
    $this->requestedUrl = '';
    $this->resolvedUrl = '';
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

  /**
   * @deprecated
   *
   * Adds an item to the contextBag.
   *
   * @param mixed $value
   *   The value to add to the bag.
   */
  public function addToContextBag($value) {
    $this->contextBag[] = $value;
  }

  /**
   * @deprecated
   *
   * Adds an item to the processedContextBag.
   *
   * @param string $key
   *   The key for the item.
   * @param mixed $value
   *   The value to add to the bag.
   */
  public function addToProcessedContextBag($key, $value) {
    $this->processedContextBag[$key] = $value;
  }

  /**
   * @deprecated
   *
   * Retrieves the processedContextBag.
   *
   * @return array
   *   The processed context bag.
   */
  public function getProcessedContextBag() {
    return $this->processedContextBag;
  }

  /**
   * @deprecated
   *
   * Clears all items in the contextBag.
   */
  public function clearContextBag() {
    $this->contextBag = [];
  }

  /**
   * @deprecated
   *
   * Clears all items in the processedContextBag.
   */
  public function clearProcessedContextBag() {
    $this->processedContextBag = [];
  }

  public function getRequestedUrl(): string {
    return $this->requestedUrl;
  }

  public function setRequestedUrl(string $requestedUrl): void {
    $this->requestedUrl = $requestedUrl;
  }

  public function getResolvedUrl(): string {
    return $this->resolvedUrl;
  }

  public function setResolvedUrl(string $resolvedUrl): void {
    $this->resolvedUrl = $resolvedUrl;
  }

  /**
   * @deprecated
   *
   * @return bool
   */
  public function contextBagIsEmpty(): bool  {
    return count($this->contextBag) === 0;
  }

  public function isEmpty(string $key): bool {
    return (!$this->getContextBag($key) || $this->getContextBag($key)->isEmpty());
  }

}
