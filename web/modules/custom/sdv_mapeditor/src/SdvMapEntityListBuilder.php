<?php

namespace Drupal\sdv_mapeditor;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Map entities.
 *
 * @ingroup sdv_mapeditor
 */
class SdvMapEntityListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Map ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\sdv_mapeditor\Entity\SdvMapEntity $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.sdv_map.edit_form',
      ['sdv_map' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
