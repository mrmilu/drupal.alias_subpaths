# Path Alias Arguments

Drupal module to allow nodes to recieve additional URL arguments.

Example:

There is a node with id 22 (`/node/22`) which alias is `/my-page`.
This module allows to resolve to this node URLs like:
- `my-page/my-taxonomy-term`
- `my-page/another-page`
- `my-page/another-page/my-taxonomy-term`

This URL entities (taxonomy, content, etc.) are stored into request attributes to be used later.

There is a submodule called `alias_subpaths_examples` that uses it into `hook_preprocess_node`:

```php
/**
 * Implements hook_preprocess_node().
 */
function alias_subpaths_example_preprocess_node(array &$variables) {
  $node = $variables['node'];
  if ($node->bundle() === 'filtered_page') {
    // Set alias subpaths arguments as node variables.
    $variables['alias_subpaths_arguments'] = \Drupal::service('alias_subpaths.context_manager')->getContextBag();
  }
}
```
