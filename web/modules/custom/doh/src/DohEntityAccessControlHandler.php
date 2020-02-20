<?php

namespace Drupal\doh;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Doh entity entity.
 *
 * @see \Drupal\doh\Entity\DohEntity.
 */
class DohEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\doh\Entity\DohEntityInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished doh entity entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published doh entity entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit doh entity entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete doh entity entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add doh entity entities');
  }


}
