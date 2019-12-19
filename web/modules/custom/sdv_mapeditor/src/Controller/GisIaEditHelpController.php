<?php

namespace Drupal\sdv_mapeditor\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class GisIaEditHelpController.
 */
class GisIaEditHelpController extends ControllerBase {

  /**
   * /admin/help/sdv_gis.
   *
   * @return string
   *   Returns twig file containing the help texts for the gis ia edit page.
   */
  public function build() {
    return [
      '#theme' => 'gis_ia_edit_help',
      '#children' => [],
    ];
  }

}
