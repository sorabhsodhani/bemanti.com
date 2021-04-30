<?php

namespace Drupal\simple_user_group_tasks;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of User Tasks[D[D[D[D[DGroup Tasks entities.
 *
 * @ingroup simple_user_group_tasks
 */
class UserTasksEntityListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('User Tasks[D[D[D[D[DGroup Tasks ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\simple_user_group_tasks\Entity\UserTasksEntity $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.user_tasks.edit_form',
      ['user_tasks' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
