<?php
declare(strict_types=1);
namespace Drupal\cxdx_pretreatment\Preprocess;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\path_alias\AliasManagerInterface;
use Drupal\node\NodeInterface;
use Drupal\cxdx_pretreatment\Service\CxdxPretreatmentService;

/**
 * Handle all node preprocesses.
 *
 *@see template_preprocess_node
 */
final class NodePreprocess extends PreprocessBase {

  /**
   * The alias manager.
   *
   * @var \Drupal\path_alias\AliasManagerInterface
   */
  protected AliasManagerInterface $aliasManager;

  /**
   * LocalBlocStorage service
   *
   * @var \Drupal\cxdx_pretreatment\Service\CxdxPretreatmentService
   */
  private CxdxPretreatmentService $pretreatment_service;

  /**
   * @param \Drupal\path_alias\AliasManagerInterface $alias_manager
   *    The alias manager.
   * @param \Drupal\cxdx_pretreatment\Service\CxdxPretreatmentService $pretreatment_service
   *    The CxdxPreprocessService service
   */
  public function __construct(AliasManagerInterface $alias_manager, CxdxPretreatmentService $pretreatment_service) {
    $this->aliasManager = $alias_manager;
    $this->pretreatment_service = $pretreatment_service;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container): NodePreprocess {
    return new static(
      $container->get('path_alias.manager'),
      $container->get('cxdx_pretreatment.builder')
    );
  }

  /**
   * {@inheritDoc}
   */
  public function main(array &$variables): void {
    // Get current node
    $node = $variables['node'];
    // Stire the id and the bundle
    $nid = $node->id();
    $bundle = $node->bundle();
    // Store the nid and the bunble of the content type
    $variables['nid'] = $nid;
    $variables['bundle'] = $bundle;
  }
}
