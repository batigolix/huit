<?php

namespace Drupal\sdv_mapeditor\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\migrate\Plugin\migrate\process\Download;
use Drupal\sdv_mapeditor\DownloadUrlInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class DefaultForm.
 */
class DefaultForm extends ConfigFormBase {

  public function __construct(ConfigFactoryInterface $config_factory, DownloadUrlInterface $downloadUrl) {
    parent::__construct($config_factory);
    $this->downloadUrl = $downloadUrl;
  }


  public static function create(ContainerInterface $container) {

    $downloadUrl = $container->get('sdv_mapeditor.download_url');
    return new static($container->get('config.factory'), $downloadUrl);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'sdv_mapeditor.default',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'default_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('sdv_mapeditor.default');
    $form['test'] = [
      '#markup' => $this->t('Define the GIS IA library. These will be downloaded and extracted from a zip file (preferably from github)'),
      '#weight'=>-10,
    ];
    $form['folder'] = [
      '#type' => 'textfield',
      '#length' => 60,
      '#title' => $this->t('GIS Folder'),
      '#default_value' => $config->get('folder'),
      '#description' => $this->t('Folder where to extract and store the GIS IA library files. This must start with public:// . Files will be stored in the public files directory'),
      '#weight'=>10,
    ];
    $form['version'] = [
      '#type' => 'textfield',
      '#size' => 15,
      '#title' => $this->t('Version'),
      '#default_value' => $config->get('version') ? $config->get('version') : '1.0',
      '#description' => $this->t('Version number of the GIS IA library'),
      '#weight'=>-8,
    ];
    $form['url'] = [
      '#type' => 'url',
      '#title' => $this->t('GIS library URL'),
      '#default_value' => $config->get('url') ? $config->get('url') : 'https://github.com/rivm-syso/sdv-gis/archive/master.zip',
      '#description' => $this->t('URL of the GIS library. This will be stored and extracted in the GIS folder. Preferably a ip file'),
      '#weight'=>0,
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (substr($form_state->getValue('folder'), 0, 9) !== 'public://' ) {
      $form_state->setErrorByName('folder', $this->t('Folder name must start with public://'));
    }
    if (substr($form_state->getValue('url'), -strlen('.zip')) !=='.zip') {
      $form_state->setErrorByName('folder', $this->t('File extension must be .zip'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $path = $this->downloadUrl->createFolder($form_state->getValue('folder'));

    if($path) {
//      $this->downloadUrl->download($form_state->getValue('gis_ia_url'), $path);
      $file = $this->downloadUrl->download($form_state->getValue('url'), $path);

      if ($file) {
        $this->downloadUrl->extract($file, $path);
      }


    }

    $this->config('sdv_mapeditor.default')
      ->set('url', $form_state->getValue('url'))
      ->set('folder', $form_state->getValue('folder'))
      ->save();




  }


}
