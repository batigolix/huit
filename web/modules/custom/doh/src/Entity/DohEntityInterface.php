<?php

namespace Drupal\doh\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Doh entity entities.
 *
 * @ingroup doh
 */
interface DohEntityInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Doh entity name.
   *
   * @return string
   *   Name of the Doh entity.
   */
  public function getName();

  /**
   * Sets the Doh entity name.
   *
   * @param string $name
   *   The Doh entity name.
   *
   * @return \Drupal\doh\Entity\DohEntityInterface
   *   The called Doh entity entity.
   */
  public function setName($name);

  /**
   * Gets the Doh entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Doh entity.
   */
  public function getCreatedTime();

  /**
   * Sets the Doh entity creation timestamp.
   *
   * @param int $timestamp
   *   The Doh entity creation timestamp.
   *
   * @return \Drupal\doh\Entity\DohEntityInterface
   *   The called Doh entity entity.
   */
  public function setCreatedTime($timestamp);

}
