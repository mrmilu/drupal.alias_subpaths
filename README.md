# Alias Subpaths

Drupal module to allow route aliases to recieve additional URL arguments.
This module defines a new Drupal plugin system to allow developers which routes
can recieve arguments.

Arguments are stored into a Drupal service `alias_subpaths.context_manager`.
This service has 2 arrays (called bags) that stores raw arguments and
the processed arguments, and it is available to use it anywhere.

```php
\Drupal::service('alias_subpaths.context_manager')->getContextBag();
\Drupal::service('alias_subpaths.context_manager')->getProcessedContextBag();
```

## Usage

There is an example into `alias_subpath_node` submodule that enable argument
processor for node routes:

```php
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
  }

}
```

To create a new one we only have to create a plugin like this with the
`route_name` that we want to enable, and implement `getId()` function to
identify each route into your `ArgumentResolverHandler`.

`ArgumentResolverHandler` is a class that defines for each route id what
argument types are allowed. By default, this module provides an argument
resolver handler based on `settings.php` variables, but it can be changed for
a custom one overriding this `settings.php` variable:

```php
$settings['alias_subpaths__argument_resolver_handler_class'] = '\Drupal\alias_subpaths\ArgumentResolverHandler\SettingsArgumentResolverHandler';
```

Ensure that your custom class implements `ArgumentResolverHandlerInterface`.

### SettingsArgumentResolver

To set up what argument types are available for each route you have to override
`alias_subpaths__allowed_arguments_types` variable
in `settings.php`. This variable is an array like this:

```php
$settings['alias_subpaths__allowed_arguments_types'] = [
  'route_1' => [
    'argument_type_1',
    'argument_type_2',
    //...
    'argument_type_n',
  ],
  'route_2' => [
    //...
  ],
  //...
];
```

If we use `alias_subpaths_node` module, route ids are segmented by bundle like
this: `entity:node:{bundle}`.

Then you have to set up a new array variable called
`alias_subpaths__argument_resolver_class` in `settings.php` to map each
argument type with a class to resolve it which has to
implement `ArgumentResolverInterface`:

```php
$settings['alias_subpaths__argument_resolver_class'] = [
  'argument_type_1' => '\Drupal\my_project\Plugin\ArgumentResolver\ArgumentType1Resolver',
  'argument_type_2' => '\Drupal\my_project\Plugin\ArgumentResolver\ArgumentType2Resolver',
  //...
  'argument_type_3' => '\Drupal\my_project\Plugin\ArgumentResolver\ArgumentTypeNResolver',
];
```

If there is not defined an argument resolver, module provides one by
default (`DefaultArgumentResolver`) that does nothing with received value.

## Example

We have a content type called Filtered Page (`filtered_page`) and we want to
enable alias subpaths for it.
We want to be able to recieve nodes of type `page` or `article`, or taxonomy
terms of vocabulary `tags`.

Also, we want to enable alias subpaths for taxonomy terms of vocabulary `tags`
to process arguments of type node `page`.

To resolve arguments we want to use this fields for each argument type:
- Node of type Page: a field called `field_url_key`
- Node of type Article: node title
- Taxonomy term of tags vocabulary: name of the term

Once we have installed `alias_subpaths_node` and `alias_subpaths_taxonomy_term`
modules, we have to fill this configuration on `settings.php`:

```php
// Specify ArgumentResolverHandler (Optional)
$settings['alias_subpaths__argument_resolver_handler_class'] = '\Drupal\alias_subpaths\ArgumentResolverHandler\SettingsArgumentResolverHandler';

// Set allowed argument types for our project
$settings['alias_subpaths__allowed_arguments_types'] = [
  'entity:node:filtered_page' => [
    'entity:node:page',
    'entity:node:article',
    'entity:taxonomy_term:tags',
  ],
  'entity:taxonomy_term:tags' => [
    'entity:node:page',
  ]
];

// Set the resolver classes for each argument type
$settings['alias_subpaths__argument_resolver_class'] = [
  'entity:node:page' => '\Drupal\my_project\Plugin\ArgumentResolver\NodePageArgumentResolver',
  'entity:node:article' => '\Drupal\my_project\Plugin\ArgumentResolver\NodeArticleArgumentResolver',
  'entity:taxonomy_term:tags' => '\Drupal\my_project\Plugin\ArgumentResolver\TaxonomyTermTagsArgumentResolver',
];
```

Then, you have to implement the argument resolvers in your proyect with your
custom logic. For this example all of them are into a custom module called
`my_project` and are like this:

- Node of type Page: search by field called field_url_key
```php
namespace Drupal\my_project\Plugin\ArgumentResolver;

use Drupal\alias_subpaths\ArgumentResolver\ArgumentResolverInterface;
use Drupal\node\Entity\Node;

class NodePageArgumentResolver implements ArgumentResolverInterface {

  public function resolve($value) {
    $nids = \Drupal::entityQuery('node')
      ->accessCheck()
      ->condition('type', 'page')
      ->condition('field_url_key_arg', $value)
      ->range(0, 1)
      ->execute();
    if (empty($nids)) {
      return NULL;
    }
    return Node::load(reset($nids));
  }

}
```

- Node of type Article: search by node title
```php
namespace Drupal\my_project\Plugin\ArgumentResolver;

use Drupal\alias_subpaths\ArgumentResolver\ArgumentResolverInterface;
use Drupal\node\Entity\Node;

class NodeArticleArgumentResolver implements ArgumentResolverInterface {

  public function resolve($value) {
    $nids = \Drupal::entityQuery('node')
      ->accessCheck()
      ->condition('type', 'article')
      ->condition('title', $value)
      ->range(0, 1)
      ->execute();
    if (empty($nids)) {
      return NULL;
    }
    return Node::load(reset($nids));
  }

}
```

- Taxonomy term of vocabulary tags: search by taxonomy name
```php
namespace Drupal\my_project\Plugin\ArgumentResolver;

use Drupal\alias_subpaths\ArgumentResolver\ArgumentResolverInterface;
use Drupal\taxonomy\Entity\Term;

class TaxonomyTermTagsArgumentResolver implements ArgumentResolverInterface {

  public function resolve($value) {
    $tids = \Drupal::entityQuery('taxonomy_term')
      ->accessCheck()
      ->condition('vid', 'tags')
      ->condition('name', $value)
      ->range(0, 1)
      ->execute();
    if (empty($tids)) {
      return NULL;
    }
    return Term::load(reset($tids));
  }

}
```

**IMPORTANT: Argument Resolvers should return NULL or FALSE if there is any error
during argument resolve function like the entity doesn't exist. It is needed to
show an HTTP 404 error**

---

### Usage of arguments

To illustrate this there is a submodule called `alias_subpaths_examples` that
uses arguments into `hook_preprocess_node`:

```php
/**
 * Implements hook_preprocess_node().
 */
function alias_subpaths_examples_preprocess_node(array &$variables) {
  $node = $variables['node'];
  if ($node->bundle() === 'filtered_page') {
    // Set alias subpaths arguments as node variables.
    $variables['alias_subpaths_arguments'] = \Drupal::service('alias_subpaths.context_manager')->getContextBag();
    // Set processed alias subpaths arguments as node variables
    $variables['alias_subpaths_arguments_processed'] = \Drupal::service('alias_subpaths.context_manager')->getProcessedContextBag();
  }
}
```
