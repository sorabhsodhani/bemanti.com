<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\simple_user_group_invite\Plugin\EntityReferenceSelection;

use Drupal\user\Plugin\EntityReferenceSelection\UserSelection;
use Drupal\user\Entity\User;

/**
 * Provides specific access control for the node entity type.
 *
 * @EntityReferenceSelection(
 *   id = "default:user_by_name_excluding_current",
 *   label = @Translation("User by name selection"),
 *   entity_types = {"user"},
 *   group = "default",
 *   weight = 3
 * )
 */
class UserNameExcludingCurrent extends UserSelection {

  /**
   * {@inheritdoc}
   */
  protected function buildEntityQuery($match = NULL, $match_operator = 'CONTAINS') {
    $account = \Drupal::currentUser();
    $user_data = User::load($account->id());
    $uid = $user_data->id();
    $query = parent::buildEntityQuery($match, $match_operator);
    $handler_settings = $this->configuration['handler_settings'];
    $query->condition('uid', $uid, '!=');
    return $query;
  }

}
