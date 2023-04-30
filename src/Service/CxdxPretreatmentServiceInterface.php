<?php

/**
 * @file
 * Contains Drupal\cxdx_pretreatment\Service\CxdxPretreatmentServiceInterface.
 */

namespace Drupal\cxdx_pretreatment\Service;

/**
 * Interface CxdxPretreatmentServiceInterface.
 *
 * @package Drupal\cxdx_pretreatment\Service
 */

interface CxdxPretreatmentServiceInterface {

  /**
   * modulePathByName()
   *  Function that returns the path of a module
   *
   * @param $module_name
   *  The module name to find path
   *
   * @return string|null
   */
  function modulePathByName($module_name): ?string;

  /**
   * moduleExist()
   *  Function to test the existence of a module
   *
   * @param $module_name
   *  The module name of module to check if exist
   *
   * @return bool|null
   */
  function moduleExist($module_name): ?bool;

  /**
   * isCurrentRoute()
   *  Function that checks if the route it passes is the right one
   *
   * @param string $route
   *  The route name to check if exist
   *
   * @return bool|null
   */
  function isCurrentRoute(string $route): ?bool;

  /**
   * routeName()
   *  Function that returns the current route
   *
   * @return string|null
   */
  function routeName(): ?string;
}
