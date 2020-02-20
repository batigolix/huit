<?php

namespace Drupal\doh\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Doh entity entities.
 */
class DohEntityViewsData extends EntityViewsData {

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
