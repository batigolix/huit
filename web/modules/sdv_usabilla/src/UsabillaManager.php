<?php

namespace Drupal\sdv_usabilla;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Theme\ThemeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Entity\EntityTypeManager;

/**
 * Class UsabillaManager.
 */
class UsabillaManager {

  /**
   * The language manager service.
   *
   * @todo cleanup language manager code.
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

      // Returns the usabilla ID of the active item.
      $item = $this->entityTypeManager->getStorage('usabilla_item')->load($id);
      $this->config = $this->configFactory->get('usabilla.settings');
      return $item->getUsabillaId();
    }
  }

}
