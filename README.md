# Path Alias Arguments

Drupal module to allow nodes to recieve additional URL arguments.

Example:

There is a node with id 22 (`/node/22`) which alias is `/my-page`.
This module allows to resolve to this node URLs like:
- `my-page/my-taxonomy-term`
- `my-page/another-page`
- `my-page/another-page/my-taxonomy-term`

This URL entities (taxonomy, content, etc.) are stored into request attributes to be used later.

There is a submodule called `path_alias_arg_examples` that uses it into `hook_preprocess_node`:

```php
/**
 * Implements hook_preprocess_node().
 */
function path_alias_arg_preprocess_node(array &$variables) {
  $node = $variables['node'];
  if ($node->bundle() === 'filtered_page') {
    $variables['#cache']['contexts'][] = 'node_arguments';
    $request = \Drupal::request();

    // Set path_alias_arguments as node variables.
    $variables['path_alias_arguments'] = $request->attributes->get('path_alias_arguments');
  }
}

```
