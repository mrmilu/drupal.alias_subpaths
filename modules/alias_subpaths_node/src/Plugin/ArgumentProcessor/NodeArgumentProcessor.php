<?php

namespace Drupal\alias_subpaths_node\Plugin\ArgumentProcessor;

use Drupal\alias_subpaths\Plugin\ArgumentProcessorBase;
use Drupal\alias_subpaths\Plugin\Attribute\ArgumentProcessor;

/**
 * Provides an ArgumentProcessor for nodes.
 */
#[ArgumentProcessor(
  id: 'node', route_name: 'entity.node.canonical',
)]
class NodeArgumentProcessor extends ArgumentProcessorBase {

  protected function getId() {
    return 'entity:node:' . $this->currentRouteMatch->getParameter('node')->bundle();
    // return 'entity:node:filtered_page'; @TODO: work on this for decoupled router
  }

}
