<?php

namespace Drupal\sdv_usabilla\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;

/**
 * Provides an interface for defining Usabilla item entities.
 *
 * @ingroup sdv_usabilla
 */
interface UsabillaItemEntityInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Usabilla item name.
   *
   * @return string
   *   Name of the Usabilla item.
   */
  public function getName();

  /**
   * Gets the item's Usabilla ID.
   *
   * @return string
   *   Usabilla ID.
   */
  public function getUsabillaId();

  /**
   * Sets the Usabilla item name.
   *
   * @param string $name
   *   The Usabilla item name.
   *
   * @return \Drupal\sdv_usabilla\Entity\UsabillaItemEntityInterface
   *   The called Usabilla item entity.
   */
  public function setName($name);

  /**
   * Gets the item's publication status.
   *
   * @return bool
   *   1 if published, 0 if unpublished.
   */
  public function getStatus();

  /**
   * Gets the Usabilla item creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Usabilla item.
   */
  public function getCreatedTime();

  /**
   * Sets the Usabilla item creation timestamp.
   *
   * @param int $timestamp
   *   The Usabilla item creation timestamp.
   *
   * @return \Drupal\sdv_usabilla\Entity\UsabillaItemEntityInterface
   *   The called Usabilla item entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the item's description.
   *
   * @return string
   *   Description of the Usabilla item.
   */
  public function getDescription();

  /**
   * Gets the item's last change timestamp.
   *
   * @return int
   *   Updated timestamp of the item.
   */
  public function getChangedTime();

}
