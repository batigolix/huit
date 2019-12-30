<?php


namespace Drupal\sdv_usabilla;

/**
 * Interface UsabillaManagerInterface
 *
 * @package Drupal\sdv_usabilla
 */
interface UsabillaManagerInterface {

  /**
   * Provides the Usabilla ID of the active Usabilla item.
   *
   * @return string
   *   Usabilla ID.
   */
  public function getActiveButton();

}
