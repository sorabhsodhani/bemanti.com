<?php

namespace Drupal\simple_user_group_invite;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the User Group Invites entity.
 *
 * @see \Drupal\simple_user_group_invite\Entity\UserGroupInviteEntity.
 */
class UserGroupInviteEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\simple_user_group_invite\Entity\UserGroupInviteEntityInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished user group invites entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published user group invites entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit user group invites entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete user group invites entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add user group invites entities');
  }


}
