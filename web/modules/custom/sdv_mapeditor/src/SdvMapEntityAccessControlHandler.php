<?php

namespace Drupal\sdv_mapeditor;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Map entity.
 *
 * @see \Drupal\sdv_mapeditor\Entity\SdvMapEntity.
 */
class SdvMapEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\sdv_mapeditor\Entity\SdvMapEntityInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished map entities');
        }

        return AccessResult::allowedIfHasPermission($account, 'view published map entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit map entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'edit map entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'edit map entities');
  }

}
