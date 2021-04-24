<?php

namespace Drupal\simple_user_group_invite;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of User Groups entities.
 *
 * @ingroup simple_user_group_invite
 */
class UserGroupEntityListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('User Groups ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\simple_user_group_invite\Entity\UserGroupEntity $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.user_group.edit_form',
      ['user_group' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
