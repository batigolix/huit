<?php

namespace Drupal\sdv_usabilla\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Usabilla item entities.
 */
class UsabillaItemEntityViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }

}
