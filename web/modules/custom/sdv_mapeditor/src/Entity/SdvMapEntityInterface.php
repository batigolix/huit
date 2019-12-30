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
   * Gets the Map description.
   *
   * @return string
   *   Description of the Map.
   */
  public function getDescription();

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
   * Gets the Map last change timestamp.
   *
   * @return int
   *   Updated timestamp of the Map.
   */
  public function getChangedTime();

  /**
   * Gets the value of the gi_ia_params field in a delimited format.
   *
   * @return string
   *   Parameters in delimited format.
   */
  public function getParameters();

  /**
   * Gets the color from the value of the gi_ia_params field.
   *
   * @return string
   *   Name of the color.
   */
  public function getAppearance();

  /**
   * Gets the layer definitions in delimited format.
   *
   * @return string
   *   Layer definitions in delimited format.
   */
  public function getLayers();

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

  /**
   * Gets the map publication status.
   *
   * @return bool
   *   1 if published, 0 if not published.
   */
  public function getStatus();

}
