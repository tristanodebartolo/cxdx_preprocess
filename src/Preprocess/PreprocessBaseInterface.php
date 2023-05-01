<?php
declare(strict_types=1);
namespace Drupal\cxdx_pretreatment\Preprocess;

/**
 * Provides an interface for PreprocessBase  class.
 *
 * @ingroup plugin_api
 */
interface PreprocessBaseInterface {

  /**
   * The main method.
   *
   * This the only method that needs to be
   * executed inside the theme preprocess function,
   * and it can preprocess the template variables .
   *
   * @param array $variables
   *   The variables passed to the page template
   *   throw template_preprocess_{theme}.
   */
  public function main(array &$variables): void;

}
