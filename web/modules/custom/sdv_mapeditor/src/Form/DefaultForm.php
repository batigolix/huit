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
    $form['folder'] = [
      '#type' => 'textfield',
      '#length' => 60,
      '#title' => $this->t('GIS Folder'),
      '#default_value' => $config->get('folder'),
      '#description' => $this->t('Folder where to store the GIS JavaScript files. This will be located in the public files directory'),
    ];
    $form['gis_url'] = [
      '#type' => 'url',
      '#title' => $this->t('GIS URL'),
      '#default_value' => $config->get('gis_url'),
      '#description' => $this->t('URL of the GIS JavaScript. This will be stored in the GIS folder'),
    ];
    $form['gis_ia_url'] = [
      '#type' => 'url',
      '#title' => $this->t('GIS IA URL'),
      '#default_value' => $config->get('gis_ia_url') ? $config->get('gis_ia_url') : 'https://github.com/rivm-syso/sdv-gis/raw/master/js/gis_ia.js',
      '#description' => $this->t('URL of the GIS IA JavaScript. This will be stored in the GIS folder'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $path = $this->downloadUrl->createFolder($form_state->getValue('folder'));

    if($path) {
      $this->downloadUrl->download($form_state->getValue('gis_ia_url'), $path);
      $this->downloadUrl->download($form_state->getValue('gis_url'), $path);
    }

    $this->config('sdv_mapeditor.default')
      ->set('url', $form_state->getValue('gis_ia_url'))
      ->set('url', $form_state->getValue('gis_url'))
      ->set('folder', $form_state->getValue('folder'))
      ->save();




  }

}
