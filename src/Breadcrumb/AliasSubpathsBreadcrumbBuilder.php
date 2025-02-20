<?php

namespace Drupal\alias_subpaths\Breadcrumb;

use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Routing\AdminContext;
use Drupal\Core\Routing\RouteMatchInterface;

/**
 * Provides a breadcrumb builder for alias subpaths routes.
 */
class AliasSubpathsBreadcrumbBuilder implements BreadcrumbBuilderInterface {

  /**
   * The admin context service.
   *
   * @var \Drupal\Core\Routing\AdminContext
   */
  protected $adminContext;

  /**
   * Constructs a new AliasSubpathsBreadcrumbBuilder.
   *
   * @param \Drupal\Core\Routing\AdminContext $admin_context
   *   The admin context service.
   */
  public function __construct(AdminContext $admin_context) {
    $this->adminContext = $admin_context;
  }

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match) {
    return $route_match->getRouteObject()->getOption('_alias_subpaths_route');
  }

  /**
   * {@inheritdoc}
   */
  public function build(RouteMatchInterface $route_match) {
    $breadcrumb = new Breadcrumb();
    // @todo Build breadcrumb for routes with alias subpaths.
    return $breadcrumb;
  }

}
