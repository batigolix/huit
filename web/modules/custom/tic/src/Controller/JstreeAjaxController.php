<?php

namespace Drupal\tic\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AppendCommand;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpFoundation\Request;


/**
 * Class JstreeAjaxController.
 */
class JstreeAjaxController extends ControllerBase {

  /**
   * Drupal\Core\Language\LanguageManagerInterface definition.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

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
    $instance->languageManager = $container->get('language_manager');
    $instance->entityManager = $container->get('entity.manager');
    return $instance;
  }

  /**
   * Roots.
   *
   * @return string
   *   Return Hello string.
   */
  public function roots() {

    $our_service = \Drupal::service('tic.icdapiconnect');
    $uri = 'http://example.com';
$icd_api_client = $our_service->get($uri);


    $duh = [1, 2, 3];
    $data = [];
    foreach ($duh as $dah) {
      $data[] = [
        'id' => $dah,
        'parent' => '#',
        'text' => 'parent '.$dah,
        'children'=>true,
      ];
    }
    return new JsonResponse($data);
  }

  /**
   * Children.
   *
   * @return string
   *   Return Hello string.
   */
  public function children(Request $request) {
    $duh = [4,5,6,7,8,9,10,11,12];
    $id = $request->query->get('id');
    $data = [];
    $random = rand(1,3);
    foreach ($duh as $dah) {
      $data[] = [
        'id' => $dah,
        'parent' => $id,
        'text' => 'child of ' . $id,
      ];
    }
    return new JsonResponse($data);
  }
  //    $duh = [4,5,6,7,8,9,10,11,12,13,14];
  //    $hehe = $request->query->get('id');
  //    $data = [];
  //    $random = rand(1,3);
  //    $i = 4;
  //    while($i <=15) {
  //      $data[] = [
  //        'id' => $i,
  //        'parent' => $random,
  //        'text' => $i . ' child of ' . $random,
  //      ];
  //      $i++;
  //    }

}
