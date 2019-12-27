<?php

namespace Drupal\sdv_usabilla;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Usabilla item entities.
 *
 * @ingroup sdv_usabilla
 */
class UsabillaItemEntityListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Usabilla item ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\sdv_usabilla\Entity\UsabillaItemEntity $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.usabilla_item.edit_form',
      ['usabilla_item' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
