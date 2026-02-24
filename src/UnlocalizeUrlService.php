<?php

namespace Drupal\alias_subpaths;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\language\Plugin\LanguageNegotiation\LanguageNegotiationUrl;

/**
 * Service for unlocalize URLs.
 */
class UnlocalizeUrlService {

  /**
   * The language manager service.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  private LanguageManagerInterface $languageManager;

  /**
   * The config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  private ConfigFactoryInterface $config;

  /**
   * Constructs a new UnlocalizeUrlService.
   *
   * @param \Drupal\Core\Language\LanguageManagerInterface $languageManager
   *   The Language Manager service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config
   *   The config factory service.
   */
  public function __construct(
    LanguageManagerInterface $languageManager,
    ConfigFactoryInterface $config,
  ) {
    $this->languageManager = $languageManager;
    $this->config = $config;
  }

  /**
   * Function for unlocalize a path.
   *
   * @param string $path
   *   The path to unlocalize.
   *
   * @return string
   *   The unlocalized path.
   */
  public function unlocalizeUrl($path) {
    $config = $this->config->get('language.negotiation')->get('url');

    if ($config['source'] == LanguageNegotiationUrl::CONFIG_PATH_PREFIX) {
      $parts = explode('/', trim($path, '/'));
      $prefix = array_shift($parts);

      // Search prefix within added languages.
      foreach ($this->languageManager->getLanguages() as $language) {
        if (isset($config['prefixes'][$language->getId()]) && $config['prefixes'][$language->getId()] == $prefix) {
          // Rebuild $path with the language removed.
          $path = '/' . implode('/', $parts);
          break;
        }
      }
    }

    return $path;
  }

}
