<?php

namespace Drupal\sdv_usabilla;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Theme\ThemeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Entity\EntityTypeManager;

/**
 * Class UsabillaAttach.
 */
class UsabillaAttach {

  /**
   * The language manager service.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * Drupal\Core\Config\ConfigFactory definition.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * Drupal\Core\Theme\ThemeManagerInterface definition.
   *
   * @var \Drupal\Core\Theme\ThemeManagerInterface
   */
  protected $themeManager;

  /**
   * A configuration object.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * Drupal\Core\Entity\Query\QueryFactory definition.
   *
   * @var Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * Constructs a new UsabillaAttach object.
   *
   *
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager service.
   */
  public function __construct(LanguageManagerInterface $language_manager, ConfigFactory $configFactory, ThemeManagerInterface $themeManager, EntityTypeManager $entityTypeManager) {
    $this->languageManager = $language_manager;
    $this->configFactory = $configFactory;
    $this->themeManager = $themeManager;
    $this->entityTypeManager = $entityTypeManager;
  }

  //  public static function create(ContainerInterface $container) {
  //    return new static(
  //      $container->get('entity.query')
  //    );
  //  }


//  /**
//   * Attach Usabilla button.
//   *
//   * @param array $attachments
//   *   The list of attachments. Passed by reference.
//   */
//  public function attachButton(array &$attachments) {
//
//    // Attaches Usabilla button if there is an active one.
//    $usabilla_id = $this->getActiveButton();
//    if ($usabilla_id) {
//      $attachments['#attached']['library'][] = 'sdv_usabilla/usabilla';
//      $attachments['#attached']['drupalSettings']['usabilla']['id'] = $usabilla_id;
//    }
//  }
//


  /**
   * {@inheritdoc}
   */
  public function getActiveButton() {

    // Finds active usabilla buttons.
    $query = $this->entityTypeManager->getStorage('usabilla_item')->getQuery();
    $active_theme = $this->themeManager->getActiveTheme()->getName();
    $ids = $query->condition('type', 'button')
      ->condition('status', '1')
      ->condition('theme', $active_theme, '=')
      ->condition('usabilla_id', NULL, '<>')
      ->execute();
    if ($ids) {

      // Takes the first item from the results.
      // @todo figure out way to ensure only 1 result.
      $array_values = array_values($ids);
      $id = array_shift($array_values);

      // Returns the usabilla ID of the item.
      $item = $this->entityTypeManager->getStorage('usabilla_item')->load($id);
      $this->config = $this->configFactory->get('usabilla.settings');
      return $item->getUsabillaId();
    }
  }

  /**
   * Returns JavaScript script snippet.
   *
   * @return string
   *   The script snippet.
   */
  private function scriptSnippet($container_id) {
    // Build script snippet.
    return <<<EOS

EOS;
  }

  /**
   * Check if a user is anonymous.
   *
   * @return bool
   *   If we want to add the attachment or not.
   */
  private function checkAddAttachmentIfLoggedIn() {
    //    $disableForLoggedIn = $this->config->get('disable_for_loggedin');
    $isAnonymous = \Drupal::currentUser()->isAnonymous();

    return empty($disableForLoggedIn) || $isAnonymous;
  }

}
