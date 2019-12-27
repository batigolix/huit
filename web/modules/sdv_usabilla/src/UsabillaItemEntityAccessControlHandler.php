<?php

namespace Drupal\sdv_usabilla;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Usabilla item entity.
 *
 * @see \Drupal\sdv_usabilla\Entity\UsabillaItemEntity.
 */
class UsabillaItemEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\sdv_usabilla\Entity\UsabillaItemEntityInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished usabilla item entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published usabilla item entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit usabilla item entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete usabilla item entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add usabilla item entities');
  }


}
