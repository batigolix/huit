<?php

namespace Drupal\sdv_usabilla;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Theme\ThemeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;

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
   * Constructs a new UsabillaAttach object.
   *
   *
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager service.
   */
  public function __construct(LanguageManagerInterface $language_manager, ConfigFactory $configFactory, ThemeManagerInterface $themeManager) {
    $this->languageManager = $language_manager;
    $this->configFactory = $configFactory;
    $this->themeManager = $themeManager;
  }

  /**
   * Get the script attachment.
   *
   * @param array $attachments
   *   The list of attachments. Passed by reference.
   */
  public function getAttachment(array &$attachments) {
    $this->config = $this->configFactory->get('usabilla.settings');
    $addAttachment = $this->checkAddAttachmentIfLoggedIn();

    $active_theme = $this->themeManager->getActiveTheme()->getName();
//$selected_themes = $this->config->get('themes');
    $selected_themes = ['bartik'];

    if (in_array($active_theme, $selected_themes)) {
      $language = $this->languageManager->getCurrentLanguage()->getId();
      $gtmId = $this->config->get($language . '_gtm_id');

      $attachments['#attached']['library'][] = 'sdv_usabilla/usabilla';
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
