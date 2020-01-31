<?php

namespace Drupal\tic\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class JstreeController.
 */
class JstreeController extends ControllerBase {

  /**
   * Drupal\Core\Language\LanguageManagerInterface definition.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->languageManager = $container->get('language_manager');
    return $instance;
  }

  /**
   * Jstree.
   *
   * @return string
   *   Return Hello string.
   */
  public function jstree() {



    $build['jstreecustom']['#attached']['library'][] = 'tic/jstreecustom';
    $build['jstree']['#attached']['library'][] = 'tic/jstree';

    $build['test2'] = [
      '#theme' => 'jstree',
      '#doh' => 'ksdjfhdfkjghkdf',
    ];

    $build['test'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: jstree')
    ];

    return $build;


  }

}
