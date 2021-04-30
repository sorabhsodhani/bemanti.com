<?php

namespace Drupal\simple_user_group_tasks\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\simple_user_group_tasks\Entity\UserTasksEntity;
use Drupal\user\Entity\User;
use Drupal\simple_user_group_tasks\UserGroupTasksCoreService;

/**
 * Class SocietyUserPickForm.
 */
class UserGroupTaskPickForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'user_group_task_pick_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $sid = NULL) {
    $options = [];
    
    $goal_nodes_query = \Drupal::service('entity.query')
      ->get('user_tasks')
      ->condition('user_group_id', $sid)
      ->condition('task_start_date', date("Y-m-d"));
    $goal_node_ids = $goal_nodes_query->execute();
    $goal_nodes = UserTasksEntity::loadMultiple(array_values($goal_node_ids));
    $max = 2;
    foreach ($goal_nodes as $goal_node) {
      $options[$goal_node->id()] = $goal_node->getName();
    }
    
    $form['user_group_activity'] = [
      '#type' => 'select',
      '#title' => t('Now choose an activity you want to finish within the next 24 hours'),
      '#options' => $options,
    ];
    $form['points_at_stake'] = [
      '#type' => 'number',
      '#title' => 'Pick some points that can be gained if the task is done or lost if not.',
      '#required' => true,
      '#min' => 1,
      '#max' => $max,
    ];
    $form['group_id'] = [
     '#type' => 'hidden',
     '#value' => $sid
   ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    foreach ($form_state->getValues() as $key => $value) {
      // @TODO: Validate fields.
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $id = $form_state->getValue('user_group_activity');
    $node = UserTasksEntity::load($id);
    $user = \Drupal::currentUser();
    $user_data = User::load($user->id());
    $userName = $user_data->getUsername();
    $stake_points = $form_state->getValue('points_at_stake');
    $sid = $form_state->getValue('group_id');
    
    $users_data[] = $user_data;
    $tasks_ids[] = $id;
    $task_date = date("Y-m-d");
    $save_user_pledge = UserGroupTasksCoreService::mapTasksToUsers($tasks_ids, $sid, $task_date, $users_data, $stake_points);
    if (in_array(FALSE, $save_user_pledge, TRUE)) {
      drupal_set_message('Congratulations on finding your motivation! Hope you achieve your goal!');
    }
    else {
      drupal_set_message('Something went wrong!');
    }
  }

}
