<?php

namespace Drupal\sdv_usabilla\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'WidgetBlock' block.
 *
 * This is purely experimental to show an in-page widget.
 *
 * @todo develop in-page widget as a paragraph not as a block.
 *
 * @Block(
 *  id = "widget_block",
 *  admin_label = @Translation("Widget block"),
 * )
 */
class WidgetBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal\Core\Config\ConfigManagerInterface definition.
   *
   * @var \Drupal\Core\Config\ConfigManagerInterface
   */
  protected $configManager;

  /**
   * Drupal\Core\Render\MetadataBubblingUrlGenerator definition.
   *
   * @var \Drupal\Core\Render\MetadataBubblingUrlGenerator
   */
  protected $urlGenerator;

  /**
   * Drupal\Core\Logger\LoggerChannelFactoryInterface definition.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->configManager = $container->get('config.manager');
    $instance->urlGenerator = $container->get('url_generator');
    $instance->loggerFactory = $container->get('logger.factory');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('ID'),
      '#default_value' => $this->configuration['id'],
      '#maxlength' => 64,
      '#size' => 64,
      '#weight' => '0',
    ];
    $form['snippet'] = [
      '#type' => 'textarea',
      '#title' => $this->t('snippet'),
      '#default_value' => $this->configuration['snippet'],
      '#weight' => '0',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['id'] = $form_state->getValue('id');
    $this->configuration['snippet'] = $form_state->getValue('snippet');
  }

  /**
   * Returns JavaScript script snippet.
   *
   * @return string
   *   The script snippet.
   */
  private function scriptSnippet($snippet) {
    return <<<EOS
$snippet
EOS;
  }


  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'widget_block';
    $build['content'] = [
      '#type' => 'inline_template',
      '#template' => '<div ub-in-page="{{ var }}"></div>',
      '#context' => array(
        'var' => $this->configuration['id'],
      ),
      ];

    $build['#attached']['library'][] = 'sdv_usabilla/widget';

    return $build;
  }

}
