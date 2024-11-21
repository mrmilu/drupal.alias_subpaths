<?php

namespace Drupal\alias_subpaths\Breadcrumb;

use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Routing\AdminContext;
use Drupal\Core\Routing\RouteMatchInterface;

class AliasSubpathsBreadcrumbBuilder implements BreadcrumbBuilderInterface {

  /**
   * The admin context service.
   *
   * @var \Drupal\Core\Routing\AdminContext
   */
  protected $adminContext;

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
    //TODO: Build breadcrumb for routes with alias subpaths.
    return $breadcrumb;
  }

}
