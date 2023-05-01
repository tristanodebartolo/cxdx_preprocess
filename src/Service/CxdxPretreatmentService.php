<?php

namespace Drupal\cxdx_pretreatment\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\State\StateInterface;
use Drupal\Core\Routing\CurrentRouteMatch;

/**
 * @file
 * Contains Drupal\cxdx_pretreatment\Service\CxdxPretreatmentService.
 */

/**
 * Class CxdxPretreatmentService.
 *
 * @package Drupal\cxdx_pretreatment\Service
 */
class CxdxPretreatmentService implements CxdxPretreatmentServiceInterface {

  /**
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $account;

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entity_manager;

  /**
   * The state keyvalue collection.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $module_handler;

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $current_route_match;

  /**
   * @param \Drupal\Core\Session\AccountInterface $account
   *    The current user
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_manager
   *    The entity type manager.
   * @param \Drupal\Core\State\StateInterface $state
   *    The state keyvalue collection service
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *    The module handler.
   * @param \Drupal\Core\Routing\CurrentRouteMatch $current_route_match
   *   The current route match.
   */
  public function __construct(
    AccountInterface $account,
    EntityTypeManagerInterface $entity_manager,
    StateInterface $state,
    ModuleHandlerInterface $module_handler,
    CurrentRouteMatch $current_route_match
  ) {
    $this->account = $account;
    $this->entity_manager = $entity_manager;
    $this->state = $state;
    $this->module_handler = $module_handler;
    $this->current_route_match = $current_route_match;
  }

  /**
   * {@inheritdoc}
   */
  function modulePathByName($module_name): ?string
  {
    return $this->module_handler->moduleExists($module_name) ? $this->module_handler->getModule($module_name)->getPath() : NULL;
  }

  /**
   * {@inheritdoc}
   */
  function moduleExist($module_name): ?bool
  {
    return (bool)$this->module_handler->moduleExists($module_name);
  }

  /**
   * {@inheritdoc}
   */
  function isCurrentRoute(string $route): ?bool
  {
    $route_name = $this->current_route_match->getRouteName();
    return $route_name == $route;
  }

  /**
   * {@inheritdoc}
   */
  function routeName(): ?string
  {
    return $this->current_route_match->getRouteName();
  }

}
