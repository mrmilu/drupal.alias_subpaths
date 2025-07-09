<?php

namespace Drupal\alias_subpaths;

/**
 * Manages context bags for alias subpaths.
 *
 * This class is responsible for retrieving, creating, and processing context
 * bags identified by a unique key. It uses a ContextBagFactory service to
 * create new context bags when one does not already exist for a given key.
 */
class ContextManager {

  /**
   * An associative array holding context bags.
   *
   * Each context bag is keyed by an identifier.
   *
   * @var array
   */
  protected array $contextBag;

  /**
   * The context bag factory service.
   *
   * @var \Drupal\alias_subpaths\ContextBagFactory
   */
  private ContextBagFactory $contextBagFactory;

  /**
   * @var \Drupal\alias_subpaths\UnlocalizeUrlService
   */
  private UnlocalizeUrlService $unlocalizeUrlService;

  /**
   * Constructs a new ContextManager.
   *
   * @param \Drupal\alias_subpaths\ContextBagFactory $contextBagFactory
   *   The factory service used to create new context bags.
   * @param \Drupal\alias_subpaths\UnlocalizeUrlService $unlocalizeUrlService
   */
  public function __construct(
    ContextBagFactory $contextBagFactory,
    UnlocalizeUrlService $unlocalizeUrlService
  ) {
    $this->contextBag = [];
    $this->contextBagFactory = $contextBagFactory;
    $this->unlocalizeUrlService = $unlocalizeUrlService;
  }

  /**
   * Retrieves the context bag for the specified key.
   *
   * If a context bag does not already exist for the given key, a new one is
   * created using the context bag factory.
   *
   * @param mixed $key
   *   The unique identifier for the context bag.
   *
   * @return \Drupal\alias_subpaths\ContextBag
   *   The context bag associated with the given key.
   */
  public function getContextBag($key): ContextBag {
    $key = $this->unlocalizeUrlService->unlocalizeUrl($key);
    if (array_key_exists($key, $this->contextBag)) {
      return $this->contextBag[$key];
    }
    $this->contextBag[$key] = $this->contextBagFactory->create();
    return $this->contextBag[$key];
  }

  /**
   * Processes the context bag for the given key.
   *
   * This method retrieves the context bag identified by the key and processes
   * it, returning the processed context data.
   *
   * @param mixed $key
   *   The unique identifier for the context bag.
   *
   * @return array
   *   The processed context data.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   *   Thrown if there is an error processing the context bag.
   */
  public function processContextBag($key): array {
    $key = $this->unlocalizeUrlService->unlocalizeUrl($key);
    return $this->getContextBag($key)->process();
  }

  /**
   * Determines whether the context bag for the given key is empty.
   *
   * @param string $key
   *   The unique identifier for the context bag.
   *
   * @return bool
   *   TRUE if the context bag is empty, FALSE otherwise.
   */
  public function isEmpty(string $key): bool {
    $key = $this->unlocalizeUrlService->unlocalizeUrl($key);
    return $this->getContextBag($key)->isEmpty();
  }

}
