<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\simple_user_group_invite\Plugin\EntityReferenceSelection;

use Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection;
use Drupal\user\Entity\User;

/**
 * Provides specific access control for the node entity type.
 *
 * @EntityReferenceSelection(
 *   id = "default:user_group_by_owner_id",
 *   label = @Translation("User Group By author Selection"),
 *   entity_types = {"user_group"},
 *   group = "default",
 *   weight = 3
 * )
 */
class UserGroupSelection extends DefaultSelection {

  /**
   * {@inheritdoc}
   */
  protected function buildEntityQuery($match = NULL, $match_operator = 'CONTAINS') {
    $account = \Drupal::currentUser();
    $user_data = User::load($account->id());
    $uid = $user_data->id();
    $query = parent::buildEntityQuery($match, $match_operator);
    $handler_settings = $this->configuration['handler_settings'];
    $query->condition('user_id', $uid, '=');
    return $query;
  }

}
