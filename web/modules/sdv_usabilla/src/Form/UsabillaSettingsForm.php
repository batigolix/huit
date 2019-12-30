<?php

namespace Drupal\sdv_usabilla\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Extension\ThemeHandler;

/**
 * Class UsabillaSettingsForm.
 */
class UsabillaSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'usabilla.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'usabilla_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('usabilla.settings');

    $form['access_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Access key'),
      '#size' => 36,
      '#description' => $this->t('Usabilla API access key. <strong>Not implemented yet</strong>'),
      '#default_value' => $config->get('access_key'),
    ];
    $form['secret_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Secret key'),
      '#size' => 72,
      '#description' => $this->t('Usabilla API secret key. <strong>Not implemented yet</strong>'),
      '#default_value' => $config->get('secret_key'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('usabilla.settings')
      ->set('access_key', $form_state->getValue('access_key'))
      ->set('secret_key', $form_state->getValue('secret_key'))
      ->save();
  }

}
