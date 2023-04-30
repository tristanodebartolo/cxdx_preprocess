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
  protected $acc;

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $et;

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
  protected $mh;

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $crm;

  /**
   * @param \Drupal\Core\Session\AccountInterface $acc
   *    The current user
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $et
   *    The entity type manager.
   * @param \Drupal\Core\State\StateInterface $state
   *    The state keyvalue collection service
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $mh
   *    The module handler.
   * @param \Drupal\Core\Routing\CurrentRouteMatch $crm
   *   The current route match.
   */
  public function __construct(
    AccountInterface $acc,
    EntityTypeManagerInterface $et,
    StateInterface $state,
    ModuleHandlerInterface $mh,
    CurrentRouteMatch $crm
  ) {
    $this->acc = $acc;
    $this->et = $et;
    $this->state = $state;
    $this->mh = $mh;
    $this->crm = $crm;
  }

  /**
   * {@inheritdoc}
   */
  function modulePathByName($module_name): ?string
  {
    return $this->mh->moduleExists($module_name) ? $this->mh->getModule($module_name)->getPath() : NULL;
  }

  /**
   * {@inheritdoc}
   */
  function moduleExist($module_name): ?bool
  {
    return (bool)$this->mh->moduleExists($module_name);
  }

  /**
   * {@inheritdoc}
   */
  function isCurrentRoute(string $route): ?bool
  {
    $route_name = $this->crm->getRouteName();
    return $route_name == $route;
  }

  /**
   * {@inheritdoc}
   */
  function routeName(): ?string
  {
    return $this->crm->getRouteName();
  }

}
