<?php

namespace Drupal\doh\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class JstreeController.
 */
class JstreeController extends ControllerBase {

  /**
   * Jstree.
   *
   * @return string
   *   Return Hello string.
   */
  public function jstree() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: jstree')
    ];
  }

}
