<?php

namespace Drupal\sdv_highmaps;

use Drupal\Core\Config\ConfigManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Class mapManager.
 */
class mapManager implements mapManagerInterface {

  /**
   * Drupal\Core\Config\ConfigManagerInterface definition.
   *
   * @var \Drupal\Core\Config\ConfigManagerInterface
   */
  protected $configManager;

  /**
   * Drupal\Core\Config\ConfigFactoryInterface definition.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a new mapManager object.
   */
  public function __construct(ConfigManagerInterface $config_manager, ConfigFactoryInterface $config_factory) {
    $this->configManager = $config_manager;
    $this->configFactory = $config_factory;
  }

  public function getMapTypes() {
    return [
      'nl-provinces' => [
        'name' => t('Netherlands provinces'),
        'library' => 'sdv_highmaps/nl-provinces',
        'join' => 'PROVNR',
      ],
      'nl-municipalities' => [
        'name' => t('Netherlands municipalities'),
        'library' => 'sdv_highmaps/nl-municipalities',
        'join' => 'gemnr',
      ],
      'europe' => [
        'name' => t('Europe'),
        'library' => 'sdv_highmaps/europe',
        'join' => 'iso-a2',
      ],
    ];
  }

  public function getMapOptions() {
    $map_options = [];
    foreach ($this->getMapTypes() as $key => $map) {
      $map_options[$key] = $map['name'];
    }
    return $map_options;
  }

  /**
   * Provides the map config.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   JSON containing map config.
   */
  public function getMapConfig($item) {
    // The text value has no text format assigned to it, so the user input
    // should equal the output, including newlines.
    return $item->mapconfig_json;
  }

  /**
   * Prepares the dataset for highchart map.
   *
   *
   * @param $series
   *
   * @return array dataset
   */
  public function getMapData($series) {
    $lines = explode(PHP_EOL, $series['data']);

    // Sets key and value columns and substracts 1 to match php array.
    $key_column = $series['key_column'] - 1;
    $value_column = $series['value_column'] - 1;

    // Sets the tab delimiter.
    $delimiter=$series['delimiter']='\t' ? "\t": $series['delimiter'];

    $dataset = [];
    foreach ($lines as $line) {
      $values = str_getcsv($line, $delimiter);

      // Assigns keys and values in the right order to the dataset so that
      // it is ready to be used by highcharts. User can switch key and value
      // column of the csv in the form.
      foreach ($values as $key => $value) {

        // If the key corresponds with the key column cast value as string.
        if($key == $key_column) {
          $values[0] = $value;
        }

        // If the key corresponds with the value column cast value as integer.
        if($key == $value_column) {
          $values[1] = (float) $value;
        }
      }

      // Removes redundant columns.
      $values = array_slice($values, 0, 2);
      $dataset[] = $values;
    }
    return $dataset;
  }


}
