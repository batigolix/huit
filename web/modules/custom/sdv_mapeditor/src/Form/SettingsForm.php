<?php

namespace Drupal\sdv_mapeditor\Form;

use Drupal\Component\Serialization\Exception\InvalidDataTypeException;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Serialization\Yaml;
use Drupal\migrate\Plugin\migrate\process\Download;
use Drupal\sdv_mapeditor\FileHandlerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Component\Serialization\Json;

/**
 * Class SettingsForm.
 */
class SettingsForm extends ConfigFormBase {

  public function __construct(ConfigFactoryInterface $config_factory, FileHandlerInterface $fileHandler) {
    parent::__construct($config_factory);
    $this->fileHandler = $fileHandler;
  }

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
    $form['test'] = [
      '#markup' => $this->t('Define the GIS IA library. This will be downloaded to the defined local GIS IA folder and extracted there from a zip file (preferably from github)'),
      '#weight' => -10,
    ];
    $form['folder'] = [
      '#type' => 'textfield',
      '#size' => 45,
      '#title' => $this->t('GIS IA Folder'),
      '#default_value' => $config->get('folder'),
      '#description' => $this->t('Local folder where to extract and store the GIS IA library files. This must start with public:// . Files will be stored in the public files directory'),
      '#weight' => 10,
    ];
    $form['version'] = [
      '#type' => 'textfield',
      '#size' => 15,
      '#title' => $this->t('Version'),
      '#default_value' => $config->get('version') ? $config->get('version') : '1.0',
      '#description' => $this->t('Version number of the GIS IA library'),
      '#weight' => -8,
    ];
    $form['url'] = [
      '#type' => 'url',
      '#size' => 80,
      '#title' => $this->t('GIS library URL'),
      '#default_value' => $config->get('url') ? $config->get('url') : 'https://github.com/rivm-syso/sdv-gis/archive/master.zip',
      '#description' => $this->t('URL of the GIS library. This will be stored and extracted in the GIS folder. Preferably a zip file'),
      '#weight' => 0,
    ];

    $duh = Json::decode($config->get('libraries'));
    $doh = Yaml::encode($duh);

    $libraries = $config->get('libraries') ? Json::decode($config->get('libraries')) : '';
    echo 8;

    $form['libraries'] = [
      '#type' => 'textarea',
      '#rows' => 24,
      '#required' => TRUE,
      '#title' => $this->t('Libraries'),
      '#default_value' => $doh,
      '#description' => $this->t('Version number of the GIS IA library'),
      '#weight' => -8,
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    // Validates GIS IA folder path is using public stream wrapper.
    if (substr($form_state->getValue('folder'), 0, 9) !== 'public://') {
      $form_state->setErrorByName('folder', $this->t('Folder name must start with public://'));
    }

    // Validates GIS IA library file has the .zip extension.
    if (substr($form_state->getValue('url'), -strlen('.zip')) !== '.zip') {
      $form_state->setErrorByName('folder', $this->t('File extension must be .zip'));
    }


    try {
      // Decode the submitted import.
      $libraries = Yaml::decode($form_state->getValue('libraries'));
      $doh = Yaml::encode($form_state->getValue('libraries'));
    } catch (InvalidDataTypeException $e) {
      $form_state->setErrorByName('libraries', $this->t('Settings can not be saved because of the following YAML error: %message', ['%message' => $e->getMessage()]));
    }

    $files = [];
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

    echo 2;

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Creates the GIS IA folder.
    //    $path = $this->fileHandler->createFolder($form_state->getValue('folder'));
    //
    //    // Downloads and extracts the GIS IA library.
    //    if($path) {
    //      $file = $this->fileHandler->download($form_state->getValue('url'), $path);
    //      if ($file) {
    ////        $this->fileHandler->extract($file, $path);
    //      }
    //    }


    $libraries = Yaml::decode($form_state->getValue('libraries'));
    $libraries = Json::encode($libraries);

    echo 9;

    // Saves the configuration
    $this->config('sdv_mapeditor.settings')
      ->set('url', $form_state->getValue('url'))
      ->set('folder', $form_state->getValue('folder'))
      ->set('version', $form_state->getValue('version'))
      ->set('libraries', $libraries)
      ->save();

    // Clears caches in order to rebuild library.
    drupal_flush_all_caches();

  }


}