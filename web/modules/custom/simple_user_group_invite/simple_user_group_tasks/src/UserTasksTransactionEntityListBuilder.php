<?php

namespace Drupal\simple_user_group_tasks;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of User Tasks Transaction entities.
 *
 * @ingroup simple_user_group_tasks
 */
class UserTasksTransactionEntityListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('User Tasks Transaction ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\simple_user_group_tasks\Entity\UserTasksTransactionEntity $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.user_tasks_transaction.edit_form',
      ['user_tasks_transaction' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
