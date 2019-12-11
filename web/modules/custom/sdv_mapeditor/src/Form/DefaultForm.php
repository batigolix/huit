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
      '#title' => $this->t('Folder'),
      '#default_value' => $config->get('folder'),
      '#description' => $this->t('Folder where to store the GIS JavaScript files'),
    ];
    $form['url'] = [
      '#type' => 'url',
      '#title' => $this->t('URL'),
      '#default_value' => $config->get('url'),
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
      $this->downloadUrl->download($form_state->getValue('url'), $path);
    }

    $this->config('sdv_mapeditor.default')
      ->set('url', $form_state->getValue('url'))
      ->set('folder', $form_state->getValue('folder'))
      ->save();




  }

}
