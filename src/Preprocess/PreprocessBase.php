<?php
declare(strict_types=1);
namespace Drupal\cxdx_pretreatment\Preprocess;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Generic reusable template preprocess operations.
 */
abstract class PreprocessBase implements ContainerInjectionInterface, PreprocessBaseInterface {

  /**
   * Manage drupal configurations.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  public ConfigFactoryInterface $configFactory;

  /**
   * Storing Injected PreprocessBase dependencies in class props.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   Manage drupal configurations.
   */
  public function __construct(ConfigFactoryInterface $configFactory) {
    $this->configFactory = $configFactory;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container): PreprocessBase {
    /* @phpstan-ignore-next-line */
    return new static(
      $container->get('config.factory')
    );
  }

  /**
   * Getting specific config from the config table.
   *
   * @param string $config_name
   *  The name of the config
   * @return array
   *   an array of configurations.
   *
   * @throws \Exception
   */
  protected function getConfigs(string $config_name): array {
    $config = $this->configFactory->get($config_name);

    if (count($config->getRawData()) === 0) {
      throw new \Exception('trying to access to a no existing config: ' . $config_name);
    }
    return $config->getRawData();
  }

}
