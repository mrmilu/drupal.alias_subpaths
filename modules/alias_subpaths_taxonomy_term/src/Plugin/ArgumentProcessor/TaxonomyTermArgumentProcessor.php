<?php

namespace Drupal\alias_subpaths_taxonomy_term\Plugin\ArgumentProcessor;

use Drupal\alias_subpaths\Plugin\ArgumentProcessorBase;
use Drupal\alias_subpaths\Plugin\Attribute\ArgumentProcessor;

/**
 * Provides an ArgumentProcessor for taxonomy_terms.
 */
#[ArgumentProcessor(
  id: 'taxonomy_term', route_name: 'entity.taxonomy_term.canonical',
)]
class TaxonomyTermArgumentProcessor extends ArgumentProcessorBase {

  protected function getId() {
    return 'entity:taxonomy_term:' . $this->contextBag->getRouteInfo()['arguments'][0]->bundle();
  }

}
