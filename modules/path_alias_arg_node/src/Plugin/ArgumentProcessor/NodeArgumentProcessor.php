<?php

namespace Drupal\path_alias_arg_node\Plugin\ArgumentProcessor;

use Drupal\path_alias_arg\Plugin\ArgumentProcessorBase;
use Drupal\path_alias_arg\Plugin\Attribute\ArgumentProcessor;

/**
 * Provides an ArgumentProcessor for nodes.
 */
#[ArgumentProcessor(
  id: 'node', route_name: 'entity.node.canonical',
)]
class NodeArgumentProcessor extends ArgumentProcessorBase {

  public function process() {
    // @TODO: process arguments for nodes
  }

}
