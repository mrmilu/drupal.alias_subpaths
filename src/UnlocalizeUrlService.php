<?php

namespace Drupal\alias_subpaths;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\language\Plugin\LanguageNegotiation\LanguageNegotiationUrl;

class UnlocalizeUrlService {

  /**
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  private LanguageManagerInterface $languageManager;

  /**
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  private ConfigFactoryInterface $config;

  /**
   * @param \Drupal\Core\Language\LanguageManagerInterface $languageManager
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config
   */
  public function __construct(
    LanguageManagerInterface $languageManager,
    ConfigFactoryInterface $config,
  ) {
    $this->languageManager = $languageManager;
    $this->config = $config;
  }


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
