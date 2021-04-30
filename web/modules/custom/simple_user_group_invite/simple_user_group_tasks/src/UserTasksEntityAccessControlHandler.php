<?php

namespace Drupal\simple_user_group_tasks;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the User Tasks[D[D[D[D[DGroup Tasks entity.
 *
 * @see \Drupal\simple_user_group_tasks\Entity\UserTasksEntity.
 */
class UserTasksEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\simple_user_group_tasks\Entity\UserTasksEntityInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished user tasks[d[d[d[d[dgroup tasks entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published user tasks[d[d[d[d[dgroup tasks entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit user tasks[d[d[d[d[dgroup tasks entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete user tasks[d[d[d[d[dgroup tasks entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add user tasks[d[d[d[d[dgroup tasks entities');
  }


}
