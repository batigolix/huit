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
    $config = $this->config('sdv_usabilla.usabillasettings');

    $theme_handler = \Drupal::service('theme_handler');
    $themes = $theme_handler->listInfo();
    foreach($themes as $key => $theme){
      if ($theme_handler->themeExists($key) && $theme_handler->hasUi($key)) {
        $enabled_themes[$key] = $theme->getName();
      }
    }



    $form['themes'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Themes'),
      '#description' => $this->t('Themes on which you want to make Usabilla available'),
      '#options' => $enabled_themes,
      '#default_value' => $config->get('themes'),
    ];
    $form['paths'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Pages'),
      '#description' => $this->t('Paths'),
      '#default_value' => $config->get('paths'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('sdv_usabilla.usabillasettings')
      ->set('themes', $form_state->getValue('themes'))
      ->set('paths', $form_state->getValue('paths'))
      ->save();
  }

}
