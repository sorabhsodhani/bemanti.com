<?php

namespace Drupal\simple_user_group_invite;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of User Group Invites entities.
 *
 * @ingroup simple_user_group_invite
 */
class UserGroupInviteEntityListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('User Group Invites ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\simple_user_group_invite\Entity\UserGroupInviteEntity $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.user_group_invite.edit_form',
      ['user_group_invite' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
