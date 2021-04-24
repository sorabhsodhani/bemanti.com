<?php

namespace Drupal\simple_user_group_points;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of User Group points entities.
 *
 * @ingroup simple_user_group_points
 */
class UserGroupPointsEntityListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('User Group points ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\simple_user_group_points\Entity\UserGroupPointsEntity $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.user_group_points.edit_form',
      ['user_group_points' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
