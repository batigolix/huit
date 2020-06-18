<?php

namespace Drupal\sdv_highmaps\Plugin\Field\FieldWidget;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

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
class HighmapsWidget extends WidgetBase {

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

    $element['value'] = $element + [
        '#type' => 'textfield',
        '#default_value' => isset($items[$delta]->value) ? $items[$delta]->value : NULL,
        '#size' => $this->getSetting('size'),
        '#placeholder' => $this->getSetting('placeholder'),
        '#maxlength' => $this->getFieldSetting('max_length'),
      ];

    $map_config = [
      'title' => NULL,
      'subtitle' => NULL,
      'mapNavigation' => [
        'enabled' => FALSE,
      ],
      'colorAxis' => [
        'minColor' => '#e6ebf5',
        'maxColor' => '#003399',
      ],
    ];
    $map_config = isset($items[$delta]->mapconfig_json) ? Json::decode($items[$delta]->mapconfig_json) : $map_config;
    $element['mapconfig'] = [
      '#type' => 'container',
      '#title' => t('Map configuration'),
      'title' => [
        '#type' => 'textfield',
        '#title' => t('Map title'),
        '#default_value' => $map_config['title'],
        '#size' => 64,
        '#description' => t('Map title will be printed in the top of the map'),
        '#maxlength' => 128,
      ],
      'subtitle' => [
        '#type' => 'textfield',
        '#title' => t('Map subtitle'),
        '#default_value' => $map_config['subtitle'],
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
          '#default_value' => $map_config['mapNavigation']['enabled'],
        ],
      ],
      'colorAxis' => [
        '#type' => 'fieldset',
        '#title' => t('Color axis'),
        'minColor' => [
          '#type' => 'color',
          '#title' => t('Minimum color'),
          '#description' => t('The color to represent the minimum of the color axis'),
          '#default_value' => $map_config['colorAxis']['minColor'],
        ],
        'maxColor' => [
          '#type' => 'color',
          '#title' => t('Maximum color'),
          '#description' => t('The color to represent the maximum of the color axis'),
          '#default_value' => $map_config['colorAxis']['maxColor'],
        ],
      ],
      'data' => [
        '#type' => 'fieldset',
        '#title' => t('Data'),
        'dataset' => [
          '#type' => 'textarea',
          '#rows' => 8,
          '#cols' => 10,
          '#title' => t('Dataset'),
          '#description' => t("Map dataset in CSV format. Use comma's to separate columns"),
          '#default_value' => $map_config['data']['dataset'],
        ],
       ],
    ];

    // @todo form alter with checkobox to show hide json config

    // @todo form alter with checkobox to show hide json config
    $element['mapconfig_json'] = [
      '#type' => 'textarea',
      '#rows' => 4,
      '#cols' => 15,
      '#title' => t('Map configuration'),
      '#description' => t('Map configuration in JSON format'),
      '#default_value' => isset($items[$delta]->mapconfig_json) ? $items[$delta]->mapconfig_json : NULL,
    ];

    return $element;
  }

  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    foreach ($values as $key => $value) {
      $map_config = Json::encode($value['mapconfig']);
      $values[$key]['mapconfig_json'] = $map_config;
    }
    return $values;
  }
}
