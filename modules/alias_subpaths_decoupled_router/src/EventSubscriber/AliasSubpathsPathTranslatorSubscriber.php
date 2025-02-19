<?php

namespace Drupal\alias_subpaths_decoupled_router\EventSubscriber;

use Drupal\alias_subpaths\AliasSubpathsManager;
use Drupal\alias_subpaths\Exception\InvalidArgumentException;
use Drupal\alias_subpaths\Exception\NotAllowedArgumentsException;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\decoupled_router\PathTranslatorEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * Provides a Subscriber for PathTranslator::TRANSLATE event.
 */
class AliasSubpathsPathTranslatorSubscriber implements EventSubscriberInterface {
  use StringTranslationTrait;

  /**
   * AliasSubpathsManager service.
   *
   * @var \Drupal\alias_subpaths\AliasSubpathsManager
   */
  private AliasSubpathsManager $aliasSubpathsManager;

  /**
   * Constructs a new AliasSubpathsPathTranslatorSubscriber.
   *
   * @param \Drupal\alias_subpaths\AliasSubpathsManager $alias_subpaths_manager
   *   The AliasSubpathsManager parameter.
   * @param \Drupal\Core\StringTranslation\TranslationInterface $string_translation
   */
  public function __construct(
    AliasSubpathsManager $alias_subpaths_manager,
    TranslationInterface $string_translation,
  ) {
    $this->aliasSubpathsManager = $alias_subpaths_manager;
    $this->stringTranslation = $string_translation;
  }

  /**
   * {@inheritDoc}
   */
  public static function getSubscribedEvents() {
    $events[PathTranslatorEvent::TRANSLATE][] = ['onPathTranslation'];
    return $events;
  }

  /**
   * Processes a path translation request.
   */
  public function onPathTranslation(PathTranslatorEvent $event) {
    $path = $event->getPath();
    try {
      $this->aliasSubpathsManager->resolve($path);
    }
    catch (NotAllowedArgumentsException | InvalidArgumentException | ResourceNotFoundException $exception) {
      $event->getResponse()->setData([
        'message' => $this->t(
          'Unable to resolve path @path.',
          ['@path' => $path]
        ),
        'details' => $this->t(
          'None of the available methods were able to find a match for this path.'
        ),
      ]);
      $event->getResponse()->setStatusCode(404);
      $event->stopPropagation();
    }

  }

}
