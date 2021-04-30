<?php

namespace Drupal\simple_user_group_tasks;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the User Tasks Transaction entity.
 *
 * @see \Drupal\simple_user_group_tasks\Entity\UserTasksTransactionEntity.
 */
class UserTasksTransactionEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\simple_user_group_tasks\Entity\UserTasksTransactionEntityInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished user tasks transaction entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published user tasks transaction entities');

      case 'update':
        
        $access = AccessResult::allowedIfHasPermission($account, 'edit user tasks transaction entities');
        if (!$access->isAllowed() && $account->hasPermission('edit own user group transaction entities')) {
          $access = $access->orIf(AccessResult::allowedIf($account->id() == $entity->getOwnerId())->cachePerUser()->addCacheableDependency($entity));
        }
        return $access;
      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete user tasks transaction entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add user tasks transaction entities');
  }


}
