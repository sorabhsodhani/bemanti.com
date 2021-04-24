<?php

namespace Drupal\simple_user_group_points;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the User Group points entity.
 *
 * @see \Drupal\simple_user_group_points\Entity\UserGroupPointsEntity.
 */
class UserGroupPointsEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\simple_user_group_points\Entity\UserGroupPointsEntityInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished user group points entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published user group points entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit user group points entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete user group points entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add user group points entities');
  }


}
