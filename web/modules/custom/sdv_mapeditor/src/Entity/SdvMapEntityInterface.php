<?php

namespace Drupal\sdv_mapeditor\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Map entities.
 *
 * @ingroup sdv_mapeditor
 */
interface SdvMapEntityInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Map name.
   *
   * @return string
   *   Name of the Map.
   */
  public function getName();

  /**
   * Sets the Map name.
   *
   * @param string $name
   *   The Map name.
   *
   * @return \Drupal\sdv_mapeditor\Entity\SdvMapEntityInterface
   *   The called Map entity.
   */
  public function setName($name);

  /**
   * Gets the Map creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Map.
   */
  public function getCreatedTime();

  /**
   * Sets the Map creation timestamp.
   *
   * @param int $timestamp
   *   The Map creation timestamp.
   *
   * @return \Drupal\sdv_mapeditor\Entity\SdvMapEntityInterface
   *   The called Map entity.
   */
  public function setCreatedTime($timestamp);

}
