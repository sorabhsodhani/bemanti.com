<?php

namespace Drupal\simple_user_group_tasks\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\user\Entity\User;
use Drupal\simple_user_group_tasks\Entity\UserTasksEntity;
use Drupal\simple_user_group_tasks\UserGroupTasksCoreService;

/**
 * Class SocietyAddTasksForm.
 */
class UserGroupUploadTasksForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'user_group_upload_tasks_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $sid = NULL) {
    $form['upload_tasks_csv'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Upload Tasks CSV'),
      '#weight' => '0',
      '#attributes' => ['class' => ['upload-tasks-file']],
      '#prefix' => '<div class="upload-tasks-file">',
      '#suffix' => '<span class="error-message"></span></div>',
      '#upload_validators' => [
        'file_validate_extensions' => ['csv'],
      ],
      '#ajax' => [
        'callback' => [$this, 'validateTasksFileMethod'],
        'progress' => ['type' => 'none', 'message' => NULL],
      ],
    ];
    $form['task_pick_date'] = [
      '#type' => 'date',
      '#title' => $this->t('Pick a date'),
      '#weight' => '2',
      '#attributes' => array('min'=> \Drupal::service('date.formatter')->format(strtotime("+1 day"), 'custom', 'Y-m-d') ),
    ];
   $form['group_id'] = [
     '#type' => 'hidden',
     '#value' => $sid
   ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#weight' => '3'
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
  
  public function validateTasksFileMethod() {
    $response = new AjaxResponse();
    $validators = ['file_validate_extensions' => ['csv']];
    $file = file_save_upload('upload_tasks_csv', $validators, FALSE, 0);
    if (!$file) {
      $response->addCommand(new InvokeCommand('.upload-tasks-file', 'addClass', array('error')));
      $response->addCommand(new InvokeCommand('.upload-tasks-file .error-message', 'attr', ['value', 'This should be a CSV']));
      return $ajax_response;
    }

  // The rest of my submit function. 
  }
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_tasks_file = $form_state->getValue('upload_tasks_csv', 0);
    // Read data from CSV.
    if (isset($form_tasks_file[0]) && !empty($form_tasks_file[0])) {
      $tasks_file = File::load($form_tasks_file[0]);
      $tasks_file->setPermanent();
      $tasks_file->save();
      $destination = $tasks_file->get('uri')->value;
      $tasks_data_file = fopen($destination, "r");
      $tasks_data = [];
      $row = 0;
      while (!feof($tasks_data_file)) {
        $tasks_data[$row] = fgetcsv($tasks_data_file);
        $row++;
      }
      // Unset header row.
      unset($tasks_data[0]);
    }
    $sid = $form_state->getValue('group_id');
    $task_date = $form_state->getValue('task_pick_date');
    
    // Save Tasks.
    $save_tasks = UserGroupTasksCoreService::saveTasksOfGroup($tasks_data, $sid, $task_date);
    if (in_array(FALSE, $save_tasks, TRUE)) {
      \Drupal::messenger()->addMessage('Uh oh! There is an issue with the below data you uploaded. Please check!');
    }
    else {
      \Drupal::messenger()->addMessage('Tasks are saved and mapped with respective users');
    }
  }

}
