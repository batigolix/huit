<?php

namespace Drupal\sdv_mapeditor\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class DohForm.
 */
class DohForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'sdv_mapeditor.doh',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'doh_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('sdv_mapeditor.doh');
    $form['url'] = [
      '#type' => 'url',
      '#title' => $this->t('URL'),
      '#default_value' => $config->get('url'),
    ];
    $form['path'] = [
      '#type' => 'path',
      '#title' => $this->t('Path'),
      '#default_value' => $config->get('path'),
    ];
    $form['url_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Url text'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $config->get('url_text'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('sdv_mapeditor.doh')
      ->set('url', $form_state->getValue('url'))
      ->set('path', $form_state->getValue('path'))
      ->set('url_text', $form_state->getValue('url_text'))
      ->save();
  }

}
