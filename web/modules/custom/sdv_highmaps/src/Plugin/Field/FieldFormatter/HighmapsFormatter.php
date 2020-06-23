<?php

namespace Drupal\sdv_highmaps\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\sdv_highmaps\mapManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
class HighmapsFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  /**
   * The date formatter service.
   *
   * @var \Drupal\sdv_highmaps\mapManagerInterface
   */
  protected $mapManager;

  /**
   * Constructs a new HighmapsFormatter.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Third party settings.
   * @param \Drupal\sdv_highmaps\mapManagerInterface $mapManager
   *   The date formatter service.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, mapManagerInterface $mapManager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);

    $this->mapManager = $mapManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('sdv_highmaps.manager'),
    );
  }


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
      $elements[$delta]['#attached']['drupalSettings']['highmaps']['config'] = $this->mapManager->getMapConfig($item);

      // Gets the dataset.
      $mapconfig = json_decode($item->mapconfig_json);
      $elements[$delta]['#attached']['drupalSettings']['highmaps']['dataset'] = $this->mapManager->getMapData($mapconfig->series);

      // Gets the map type.
      $maps = $this->mapManager->getMapTypes();
      // Converts map dataset into an array before attaching.
      $elements[$delta]['#attached']['drupalSettings']['highmaps']['map'] = $maps[$mapconfig->chart->map];

      // Adds geojson library.
      $map = $mapconfig->chart->map;
      $elements[$delta]['#attached']['library'][] = "sdv_highmaps/$map";
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


}


