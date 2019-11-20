<?php

namespace Drupal\sdv_mapeditor\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Map entities.
 */
class SdvMapEntityViewsData extends EntityViewsData {

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
