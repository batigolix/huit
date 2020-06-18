<?php

namespace Drupal\sdv_highcharts\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ThemesForm.
 */
class ThemesForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'sdv_highcharts.themes',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'sdv_higcharts_admin_themes_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('sdv_highcharts.themes');

    $custom_theme = '';
    $uri = 'public://sdv_highcharts/custom_themes.js';

    if (file_exists($uri)) {
      $custom_theme = file_get_contents($uri);
    } 
   
    $form['themes'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Themes'),
      '#title_display' => 'invisible',
      '#default_value' => $custom_theme, //$config->get('sdv_highcharts.themes'),
      '#rows' => 20,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->config('sdv_highcharts.themes')
      ->set('themes', $values['themes'])
      ->save();

    // Save a JS theme file to public://sdv_highcharts
    $dir = 'public://sdv_highcharts';
    file_prepare_directory($dir, FILE_CREATE_DIRECTORY);
    file_unmanaged_save_data($values['themes'], $dir . '/custom_themes.js', FILE_EXISTS_REPLACE);
    
    // cache clear to force rebuild of library
    drupal_flush_all_caches();
  }

}
