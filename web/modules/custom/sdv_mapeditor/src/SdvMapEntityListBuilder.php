<?php

namespace Drupal\sdv_mapeditor;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Link;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Component\Utility\Unicode;

/**
 * Defines a class to build a listing of Map entities.
 *
 * @ingroup sdv_mapeditor
 */
class SdvMapEntityListBuilder extends EntityListBuilder {

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * Constructs a new SdvMapEntityListBuilder object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage class.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter service.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, DateFormatterInterface $date_formatter) {
    parent::__construct($entity_type, $storage);

    $this->dateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity_type.manager')->getStorage($entity_type->id()),
      $container->get('date.formatter'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Map ID');
    $header['name'] = $this->t('Name');
    $header['description'] = $this->t('Description');
    $header['author'] = $this->t('Author');
    $header['status'] = $this->t('Status');
    $header['created'] = $this->t('Created');
    $header['changed'] = $this->t('Updated');
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
      'entity.sdv_map.canonical',
      ['sdv_map' => $entity->id()]
    );
    $row['description'] = Unicode::truncate($entity->getDescription(), 60, TRUE, TRUE);
    $row['author'] = $entity->getOwner()->label();
    $row['status'] = $entity->getStatus() == 1 ? $this->t('Published') : $this->t('Unpublished');
    $row['changed'] = $this->dateFormatter->format($entity->getChangedTime(), 'short');
    $row['created'] = $this->dateFormatter->format($entity->getCreatedTime(), 'short');
    return $row + parent::buildRow($entity);
  }

}
