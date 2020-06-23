<?php

namespace Drupal\sdv_highmaps;

use Drupal\Core\Field\FieldItemInterface;

/**
 * Interface mapManagerInterface.
 */
interface mapManagerInterface {

  /**
   * Provides list of map types.
   *
   * This can be for examples provinces, europe, etc.
   *
   * @return array List of map types containing key, name, description, library.
   */
  function getMapTypes();

  /**
   * Provides list of map that can be used as select list options.
   *
   * @return array List of map options consisting of key and label.
   */
  function getMapOptions();

  /**
   * Provides the map config.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   JSON containing map configuration.
   */
  function getMapConfig($item);

  /**
   * Prepares the dataset for highchart map.
   *
   * @param $data
   *
   * @return array dataset
   */
  function getMapData($data);

}
