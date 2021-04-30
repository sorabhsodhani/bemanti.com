<?php

namespace Drupal\simple_user_group_points;

use Drupal\simple_user_group_points\Entity\UserGroupPointsEntity;

/**
 * Class SimpleUserGroupPointsService.
 */
class SimpleUserGroupPointsService {

  /**
   * Constructs a new SimpleUserGroupPointsService object.
   */
  public function __construct() {

  }
  public static function updateUserGroupPoints ($user = NULL, $sid = NULL, $op = 'register', $amount = NULL) {
    if ($op == 'register') {
      $amount = 100;
    }
    $updated_balance = $amount + $balance;
    $user_point_entity = UserGroupPointsEntity::create([
      'name' => $user->getUsername(),
      'user_group_id' => $sid,
      'user_id' => $user->id(),
      'user_group_points_amount' => $amount,
    ]);
    if ($user_point_entity->save()) {
      return TRUE;
    }
    else {
      \Drupal::logger('user_group_points')->error('There has been an issue while updating the user uid ' . $user->id() . ' for sid ' . $sid);
    }
  }

  public static function getUserGroupBalancePoints($user = NULL, $sid = NULL) {
    $query = \Drupal::service('entity.query')
      ->get('user_group_points')
      ->condition('user_group_id', $sid)
      ->condition('user_id', $user->id())
      ->sort('created', 'DESC')
      ->range(0, 1);
    $results = $query->execute();

    if (!empty($results)) {
      $points_data = UserGroupPointsEntity::load(reset(array_values($results)));
      return $points_data->get('user_group_points_balance')->value;
    }
    else {
      return 0;
    }

  }
}
