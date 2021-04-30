<?php

namespace Drupal\simple_user_group_tasks;

use Drupal\simple_user_group_tasks\Entity\UserTasksEntity;
use Drupal\simple_user_group_points\Entity\UserGroupPointsEntity;
use Drupal\simple_user_group_tasks\Entity\UserTasksTransactionEntity;
use Drupal\user\Entity\User;



/**
 * Class UserGroupTasksCoreService.
 */
class UserGroupTasksCoreService {

  /**
   * Constructs a new UserGroupTasksCoreService object.
   */
  public function __construct() {

  }
  
  /**
   * Saves users associated to group.
   */
  public static function saveTasksOfGroup ($tasks_data = NULL, $sid = NULL, $task_date = NULL) {
    $results = [];
    foreach ($tasks_data as $task) {
      $task_entity = UserTasksEntity::create([
        'name' => $task[0],
        'user_group_task_pointfactor' => $task[1],
        'user_group_id' => $sid,
        'task_start_date' => date('Y-m-d', strtotime($task_date))
      ]);
      if ($task_entity->save()) {
        $results[] = $task_entity->id();
      }
      else {
        \Drupal::logger('user_group_task')->error('Saving of task named ' . $task[0] . ' uploaded on ' . $task_date . ' for group with ID ' . $sid . ' failed. Please check.');
        $results[] = FALSE;
      }
    }
    return $results;
  }
  
  /**
   * Saves users associated to group.
   */
  public static function saveUsersOfGroup ($users_data = NULL, $sid = NULL) {
    $flag = TRUE;
    $error_msg = [];
    foreach ($users_data as $user_data) {
      $user = User::create();
     // $user->setPassword("password");
      $user->setEmail($user_data[1]);
      $user->setUsername($user_data[2]); //This username must be unique and accept only a-Z,0-9, - _ @ .
      $user->set('field_user_display_name', $user_data[0]);
      $user->set('field_user_associated_societies', $sid);
      $user->addRole('user_group'); //E.g: authenticated
      $user->set('init', 'email');
      $user->activate();
      $violations = $user->validate();
      if ($violations != NULL) {
        if ($user->save()) {
           _user_mail_notify('register_admin_created', $user);
           UserGroupTasksCoreService::updateUserGroupPoints($user, $sid, 'register');
        }
        else {
          $error_msg[$user_data[2]] = 'Failed while saving user entity';
          \Drupal::logger('user_group_task_saving')->error('Saving of user named ' . $user_data[0] . ' for group with ID ' . $sid . ' failed. Please check.');
        }
      }
      else {
        $error_msg[$user_data[2]] = $voilations;
      }
    }
    if (!$flag) {
      return $error_msg;
    }
    else {
      return $flag;
    }
  }
  
  public static function updateUserGroupPoints ($user = NULL, $sid = NULL, $op = 'register', $amount = NULL) {
    if ($op == 'register') {
      $amount = 100;
    }
    $balance = UserGroupTasksCoreService::getUserBalancePointsByGroup($user, $sid);
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
  
    public static function getUserBalancePointsByGroup($user = NULL, $sid = NULL) {
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
  
  public static function getUserOfGroup ($sid = NULL) {
    $query = \Drupal::service('entity.query')
      ->get('user')
      ->condition('field_user_associated_societies', [$sid], 'IN');
    $userIds = $query->execute();
    return array_values($userIds);
  }
  
  
  /**
   * Map tasks to all users associated to group.
   */
  public static function mapTasksToUsers ($tasks_ids = NULL, $sid = NULL, $task_date = NULL, $users_data = NULL, $stake_points = NULL) {
    if ($users_data == NULL) {
      $userIds_of_group = UserGroupTasksCoreService::getUserOfGroup($sid);
      if (!empty($userIds_of_group)) {
        $users_data = User::loadMultiple($userIds_of_group);
      }
    }
    if ($stake_points == NULL) {
      $stake_points = 0;
    }

    $results = [];
    foreach ($users_data as $user_data) {
      foreach ($tasks_ids as $task_id) {
        if (!is_bool($task_id)) {
        
          $taskData = UserTasksEntity::load($task_id);
          if ($taskData != NULL) {
            $map_task = UserTasksTransactionEntity::create([
              'name' => $user_data->getUsername() . ' - ' . $taskData->getName(),
              'user_task_transaction_user_id' => $user_data->id(),
              'user_group_id' => $sid,
              'task_id' => $task_id,
              'task_stake_points' => $stake_points
             ]); 
            if ($map_task->save()) {
              //kint('saveddd');exit;
              $results[] = TRUE;
            }
            else {
              $results[] = FALSE;
              \Drupal::logger('user_group_map_tasks')->error('Mapping of task with id ' . $task_id . ' for user with ID ' . $user_data->id() . ' failed. Please check.');
            }
          }
        }
      }
    }
  }
  
}
