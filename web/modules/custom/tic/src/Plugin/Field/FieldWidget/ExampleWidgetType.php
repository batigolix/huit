<?php

namespace Drupal\tic\Plugin\Field\FieldWidget;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\Serializer\Encoder\JsonEncode;

/**
 * Plugin implementation of the 'example_widget_type' widget.
 *
 * @FieldWidget(
 *   id = "example_widget_type",
 *   module = "tic",
 *   label = @Translation("Example widget type"),
 *   field_types = {
 *     "example_field_type"
 *   }
 * )
 */
class ExampleWidgetType extends WidgetBase {

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

    echo 9;
//    isset($items[$delta]->value)

//    $test = [
//      'zoom' => [
//        'type' => 'fieldset',
//        'title' => t('Zooming'),
//        'elements' => [
//          'initialZoom' => [
//            'type' => 'select',
//            'title' => t('Initial zoom level'),
//            'description' => t('The starting zoom level when this map is rendered.  Restricted by min and max zoom settings.'),
//          ],
//        ]
//      ],
//    ];

    $zoom_default_settings = [
      'initialZoom'=>1,
      'minZoom'=>2,
      'maxZoom'=>3,
    ];
    $zoom_settings = isset($items[$delta]->settings) ? Json::decode($items[$delta]->settings) : $zoom_default_settings;
$zoom_options=[1=>1,2=>2,3=>3,4=>4,5=>5];

//foreach($test as $key=> $testelements) {
//
//  $element[$key];
//
//  foreach ($testelements as $testelement ) {
//
//    $element[$key][$testelement];
//
//  }
//
//}

echo 6;

    $element['zoom'] = array(
      '#type' => 'fieldset',
      '#title' => t('Zooming'),
      '#weight' => 12,
      'initialZoom' => array(
        '#title' => t('Initial zoom level'),
        '#description' => t('The starting zoom level when this map is rendered.  Restricted by min and max zoom settings.'),
        '#type' => 'select',
        '#default_value' => $zoom_settings['initialZoom'],
        '#options'=>$zoom_options,
      ),
      'minZoom' => array(
        '#title' => t('Minimum zoom level'),
        '#description' => t('The minimum zoom level allowed. (How far away can you view from?)'),
        '#type' => 'select',
        '#default_value' => $zoom_settings['minZoom'],
        '#options'=>$zoom_options,
      ),
      'maxZoom' => array(
        '#title' => t('Maximum zoom level'),
        '#description' => t('The maximum zoom level allowed. (How close in can you get?).'),
        '#type' => 'select',
        '#default_value' => $zoom_settings['maxZoom'],
        '#options'=>$zoom_options,
      ),
    );


    $element['settings'] = array(
      '#type' => 'textarea',
      '#rows' => 4,
      '#cols'=> 15,
      '#title' => t('Setttings'),
      '#default_value' => isset($items[$delta]->settings) ? $items[$delta]->settings : null,
    );

    return $element;
  }

  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {

    foreach($values as $key=>$value) {
      $zoom_values = Json::encode($value['zoom']);
//      $zoom_values = Json::encode();

      $values[$key]['settings'] = $zoom_values;
    }
    return $values;
  }
}
