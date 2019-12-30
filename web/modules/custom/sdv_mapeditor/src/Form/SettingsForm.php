<?php

namespace Drupal\sdv_mapeditor\Form;

use Drupal\Component\Serialization\Exception\InvalidDataTypeException;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Serialization\Yaml;
use Drupal\sdv_mapeditor\FileHandlerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Component\Serialization\Json;

/**
 * Class SettingsForm.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function __construct(ConfigFactoryInterface $config_factory, FileHandlerInterface $fileHandler) {
    parent::__construct($config_factory);
    $this->fileHandler = $fileHandler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $fileHandler = $container->get('sdv_mapeditor.filehandler');
    return new static($container->get('config.factory'), $fileHandler);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'sdv_mapeditor.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('sdv_mapeditor.settings');
    $form['intro'] = [
      '#markup' => $this->t('Defines the external sources used by GIS IA'),
      '#weight' => -10,
    ];

    // WMS URL provides layers from the geo-server via a GetCapabilities
    // request.
    $form['wms_url'] = [
      '#type' => 'url',
      '#size' => 80,
      '#title' => $this->t('WMS URL'),
      '#default_value' => $config->get('wms_url') ? $config->get('wms_url') : 'https://geodata.rivm.nl/geoserver/wms?VERSION=1.1.1&REQUEST=GetCapabilities',
      '#description' => $this->t('URL of the XML file containing the WMS definitions. This will be downloaded and saved to the GIS folder.'),
      '#weight' => 0,
    ];

    // Layers from a specific URL.
    $form['servers'] = [
      '#type' => 'textfield',
      '#title' => $this->t('URL'),
      '#description' => $this->t('Enter the URL(s) of the map-server(s).'),
      '#required' => TRUE,
      '#size' => 64,
      '#default_value' => $config->get('servers'),
    ];

    $form['libraries_set'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Library settings'),
      '#weight' => -8,
    ];
    $form['libraries_set']['version'] = [
      '#type' => 'textfield',
      '#size' => 15,
      '#title' => $this->t('Version'),
      '#default_value' => $config->get('version') ? $config->get('version') : '1.0',
      '#description' => $this->t('Version number of the GIS IA library'),
      '#weight' => -8,
    ];

    // Converts the libraries value from a serialized array to formatted
    // yaml before displaying.
    $storage = Json::decode($config->get('libraries'));
    $libraries = Yaml::encode($storage);
    $form['libraries_set']['libraries'] = [
      '#type' => 'textarea',
      '#rows' => 24,
      '#required' => TRUE,
      '#title' => $this->t('Libraries'),
      '#default_value' => $libraries,
      '#description' => $this->t('Version number of the GIS IA library'),
      '#weight' => -8,
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    // Validates WMS URL exists.
    if ($this->fileHandler->checkUrl($form_state->getValue('wms_url')) == FALSE) {
      $form_state->setErrorByName('wms_url', $this->t('WMS URL is not valid'));
    }

    // Validates server URLs exist.
    if ($this->fileHandler->checkUrl($form_state->getValue('servers')) == FALSE) {
      $form_state->setErrorByName('servers', $this->t('URL map server is not valid'));
    }

    // Validates YAML formatting.
    try {
      $libraries = Yaml::decode($form_state->getValue('libraries'));
    }
    catch (InvalidDataTypeException $e) {
      $form_state->setErrorByName('libraries', $this->t('Settings can not be saved because of the following YAML error: %message', ['%message' => $e->getMessage()]));
    }

    foreach ($libraries as $key => $library) {
      if (isset($library['css'])) {
        foreach ($library['css'] as $css) {

          if (!($this->fileHandler->checkIfExists($css))) {
            $form_state->setErrorByName('libraries', $this->t('File doesnt exist: %file', ['%file' => $css]));
          }

        }
      }
      if (isset($library['js'])) {
        foreach ($library['js'] as $js) {
          if (!($this->fileHandler->checkIfExists($js))) {
            $form_state->setErrorByName('libraries', $this->t('File doesnt exist: %file', ['%file' => $js]));
          }
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Converts the libraries value to a serialized array, before storage.
    $libraries = Yaml::decode($form_state->getValue('libraries'));
    $libraries = Json::encode($libraries);

    // Saves the configuration.
    $this->config('sdv_mapeditor.settings')
      ->set('wms_url', $form_state->getValue('wms_url'))
      ->set('version', $form_state->getValue('version'))
      ->set('servers', $form_state->getValue('servers'))
      ->set('libraries', $libraries)
      ->save();

    // Clears caches in order to rebuild library.
    drupal_flush_all_caches();
  }

}
