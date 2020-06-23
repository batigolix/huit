<?php

namespace Drupal\sdv_highmaps\Plugin\Field\FieldWidget;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\sdv_highmaps\mapManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Plugin implementation of the 'highmaps_widget' widget.
 *
 * @FieldWidget(
 *   id = "highmaps_widget",
 *   module = "sdv_highmaps",
 *   label = @Translation("Highmaps widget"),
 *   field_types = {
 *     "highmaps"
 *   }
 * )
 */
class HighmapsWidget extends WidgetBase implements ContainerFactoryPluginInterface {



  /**
   * {@inheritdoc}
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings, mapManagerInterface $mapManager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
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
      $configuration['third_party_settings'],
      $container->get('sdv_highmaps.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        'size' => 60,
        'placeholder' => '',
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = [];

    $elements['size'] = [
      '#type' => 'number',
      '#title' => t('Size of textfield'),
      '#default_value' => $this->getSetting('size'),
      '#required' => TRUE,
      '#min' => 1,
    ];
    $elements['placeholder'] = [
      '#type' => 'textfield',
      '#title' => t('Placeholder'),
      '#default_value' => $this->getSetting('placeholder'),
      '#description' => t('Text that will be shown inside the field until a value is entered. This hint is usually a sample value or a brief description of the expected format.'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    $summary[] = t('Textfield size: @size', ['@size' => $this->getSetting('size')]);
    if (!empty($this->getSetting('placeholder'))) {
      $summary[] = t('Placeholder: @placeholder', ['@placeholder' => $this->getSetting('placeholder')]);
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

//    $maps = $this->mapManager->getMaps();
    $element['#title'] = t('Name');
    $element['#description'] = t('Name of the map. This will be used in the admin interface.');
    $element['value'] = $element + [
        '#type' => 'textfield',
        '#default_value' => isset($items[$delta]->value) ? $items[$delta]->value : NULL,
        '#size' => $this->getSetting('size'),
        '#placeholder' => $this->getSetting('placeholder'),
        '#maxlength' => $this->getFieldSetting('max_length'),
      ];
    echo 4;
    $map_config = isset($items[$delta]->mapconfig_json) ? Json::decode($items[$delta]->mapconfig_json) : [];
    $element['mapconfig'] = [
      '#type' => 'container',
      '#title' => t('Map configuration'),
      '#element_validate' => [
        [static::class, 'validate'],
      ],
      'chart'=> [
        '#type'=>'container',
        'map'=>[
        '#type'=>'select',
        '#title' => t('Type'),
        '#options'=>$this->mapManager->getMapOptions(),
        '#default_value' => $map_config['chart']['map'] ?? 'nl-provinces',
        '#description' => t('The type of map to present data. This must be mapped with the data setusing a key.'),
        ]
      ],
      'title' => [
        '#type' => 'textfield',
        '#title' => t('Map title'),
        '#default_value' => $map_config['title'] ?? '',
        '#size' => 64,
        '#description' => t('Map title will be printed in the top of the map'),
        '#maxlength' => 128,
      ],
      'subtitle' => [
        '#type' => 'textfield',
        '#title' => t('Map subtitle'),
        '#default_value' => $map_config['subtitle'] ?? '',
        '#size' => 128,
        '#description' => t('Map subtitle will be printed in the top of the map under the title'),
        '#maxlength' => 256,
      ],
      'mapNavigation' => [
        '#type' => 'fieldset',
        '#title' => t('Map navigation'),
        'enabled' => [
          '#type' => 'checkbox',
          '#title' => t('Enable'),
          '#description' => t('Whether to enable map navigation.'),
          '#default_value' => $map_config['mapNavigation']['enabled'] ?? TRUE,
        ],
      ],
      'colorAxis' => [
        '#type' => 'fieldset',
        '#title' => t('Color axis'),
        'minColor' => [
          '#type' => 'color',
          '#title' => t('Minimum color'),
          '#description' => t('The color to represent the minimum of the color axis'),
          '#default_value' => $map_config['colorAxis']['minColor'] ?? '#e6ebf5',
        ],
        'maxColor' => [
          '#type' => 'color',
          '#title' => t('Maximum color'),
          '#description' => t('The color to represent the maximum of the color axis'),
          '#default_value' => $map_config['colorAxis']['maxColor'] ?? '#003399',
        ],
        'min' => [
          '#type' => 'number',
          '#title' => t('Minimum'),
          '#description' => t("The minimum value of the axis"),
          '#size' => 3,
          '#default_value' => $map_config['colorAxis']['min'] ?? null,
        ],
        'max' => [
          '#type' => 'number',
          '#title' => t('Maximum'),
          '#description' => t("The maximum value of the axis"),
          '#size' => 3,
          '#default_value' => $map_config['colorAxis']['max'] ?? null,
        ],
      ],
      'series' => [
        '#type' => 'fieldset',
        '#title' => t('Series'),
        'data' => [
          '#type' => 'textarea',
          '#rows' => 8,
          '#cols' => 10,
          '#title' => t('Dataset'),
          '#description' => t("Dataset in CSV format. Use comma's to separate columns or change the delimiter below. Use column 1 for the key and column 2 for the value. If necessary the order can be changed using the key and value column fields below."),
          '#default_value' => $map_config['series']['data'] ?? [],
        ],
        'delimiter' => [
          '#type' => 'textfield',
          '#title' => t('Delimiter'),
          '#default_value' => $map_config['series']['delimiter'] ?? ',',
          '#size' => 3,
          '#description' => t("Delimiter of the CSV columns. Default value is ',' (comma). For tabs enter '\\t'"),
          '#maxlength' => 8,
        ],
        'key_column' => [
          '#type' => 'number',
          '#title' => t('Key column'),
          '#description' => t("Column in the dataset that holds the key that will be used to map the data with the geojson definition."),
          '#default_value' => $map_config['series']['key_column'] ?? 1,
          '#min' => 1,
          '#size' => 3,
        ],
        'value_column' => [
          '#type' => 'number',
          '#title' => t('Value column'),
          '#description' => t("Column in the dataset that holds the values that will be shown in the map."),
          '#min' => 1,
          '#size' => 3,
          '#default_value' => $map_config['series']['value_column'] ?? 2,
        ],
        'name' => [
          '#type' => 'textfield',
          '#title' => t('Label'),
          '#default_value' => $map_config['series']['name'] ?? '',
          '#size' => 128,
          '#description' => t('Label for the value shown in the popup.'),
          '#maxlength' => 256,
        ],
        'color' => [
          '#type' => 'color',
          '#title' => t('Hover color'),
          '#default_value' => $map_config['series']['color'] ?? '#BADA55',
        ],
      ],
    ];

    // @todo form alter with checkobox to show hide json config
    $element['mapconfig_json'] = [
      '#type' => 'textarea',
      '#rows' => 4,
      '#cols' => 15,
      '#title' => t('Map configuration'),
      '#description' => t('Map configuration in JSON format'),
      '#default_value' => $items[$delta]->mapconfig_json ?? NULL,
    ];

    return $element;
  }

  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    foreach ($values as $key => $value) {

      // Removes empty coloraxis min and max from configuration to let
      // highcharts automatically calculate axis.
      if(!$value['mapconfig']['colorAxis']['min']){
        unset($value['mapconfig']['colorAxis']['min']);
      }
      if(!$value['mapconfig']['colorAxis']['max']){
        unset($value['mapconfig']['colorAxis']['max']);
      }

      $map_config = Json::encode($value['mapconfig']);




      $values[$key]['mapconfig_json'] = $map_config;
    }
    return $values;
  }

  /**
   * Validate the color text field.
   */
  public static function validate($element, FormStateInterface $form_state) {
    if ($element['series']['key_column']['#value'] === $element['series']['value_column']['#value']) {
      $form_state->setError($element['series']['key_column'], t("Key and value column cannot be the same"));
    }
  }

}

