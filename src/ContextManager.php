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
   * Array to hold the processed context.
   *
   * @var array
   */
  protected $processedContextBag;

  private string $requestedUrl;

  private string $resolvedUrl;

  /**
   * Constructor for ContextManager.
   */
  public function __construct() {
    $this->contextBag = [];
    $this->processedContextBag = [];
    $this->requestedUrl = '';
    $this->resolvedUrl = '';
  }

  /**
   * Adds an item to the contextBag.
   *
   * @param mixed $value
   *   The value to add to the bag.
   */
  public function addToContextBag($value) {
    $this->contextBag[] = $value;
  }

  /**
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
   * Retrieves the contextBag.
   *
   * @return array
   *   The context bag.
   */
  public function getContextBag() {
    return $this->contextBag;
  }

  /**
   * Retrieves the processedContextBag.
   *
   * @return array
   *   The processed context bag.
   */
  public function getProcessedContextBag() {
    return $this->processedContextBag;
  }

  /**
   * Clears all items in the contextBag.
   */
  public function clearContextBag() {
    $this->contextBag = [];
  }

  /**
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

}
