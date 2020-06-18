<?php

namespace Drupal\sdv_highmaps\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'highmaps_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "highmaps_formatter",
 *   label = @Translation("Highmaps formatter"),
 *   field_types = {
 *     "highmaps"
 *   }
 * )
 */
class HighmapsFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      // Implement default settings.
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    return [
      // Implement setting    s form.
    ] + parent::settingsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    // Implement settings summary.

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    $elements['#attached']['library'][] = 'sdv_highmaps/sdv_highmaps';
    foreach ($items as $delta => $item) {
      $elements[$delta] = ['#markup' => $this->viewValue($item)];
      $elements[$delta]['map'] = [
        '#theme' => 'sdv_highmaps',
      ];
      // @todo figure out settings for multiple items.
      $elements[$delta]['#attached']['drupalSettings']['highmaps'] = $this->getMapConfig($item);

      // Converts map dataset into an array before attaching.
      $mapconfig = json_decode($item->mapconfig_json);
      $elements[$delta]['#attached']['drupalSettings']['highmaps_data'] = $this->getMapData($mapconfig->data);
    }
    return $elements;
  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item) {
    // The text value has no text format assigned to it, so the user input
    // should equal the output, including newlines.
    return nl2br(Html::escape($item->value));
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
  protected function getMapConfig(FieldItemInterface $item) {
    // The text value has no text format assigned to it, so the user input
    // should equal the output, including newlines.
    return $item->mapconfig_json;
  }

  protected function getMapData($data) {
    $lines = explode(PHP_EOL, $data->dataset);
    $array = [];
    foreach ($lines as $line) {
      $array[] = str_getcsv($line);
    }
    return $array;
  }
}


