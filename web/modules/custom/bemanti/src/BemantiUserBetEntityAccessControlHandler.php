<?php

namespace Drupal\bemanti;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Bemanti user bet entity entity.
 *
 * @see \Drupal\bemanti\Entity\BemantiUserBetEntity.
 */
class BemantiUserBetEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\bemanti\Entity\BemantiUserBetEntityInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished bemanti user bet entity entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published bemanti user bet entity entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit bemanti user bet entity entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete bemanti user bet entity entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add bemanti user bet entity entities');
  }


}
