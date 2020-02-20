<?php

namespace Drupal\tic\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CbsOpendataController.
 */
class CbsOpendataController extends ControllerBase {

  /**
   * Drupal\Core\Config\ConfigManagerInterface definition.
   *
   * @var \Drupal\Core\Config\ConfigManagerInterface
   */
  protected $configManager;

  /**
   * Drupal\Core\Logger\LoggerChannelFactoryInterface definition.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * Drupal\Core\Entity\EntityManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->configManager = $container->get('config.manager');
    $instance->loggerFactory = $container->get('logger.factory');
    $instance->entityManager = $container->get('entity.manager');
    return $instance;
  }

  /**
   * Build.
   *
   * @return string
   *   Return Hello string.
   */
  public function build() {

    $data = file_get_contents('http://opendata.cbs.nl/ODataApi/OData/84545NED/UntypedDataSet');



    $cat_facts = json_decode($data, TRUE);

    foreach ($cat_facts as $cat_fact) {
      print "<h3>".$cat_fact['text']."</h3>";
    }


    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: build')
    ];
  }

}
