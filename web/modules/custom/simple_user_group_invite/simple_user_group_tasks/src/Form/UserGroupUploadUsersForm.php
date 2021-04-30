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
 * Class SocietyAddUsersForm.
 */
class UserGroupUploadUsersForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'user_group_upload_users_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $sid = NULL) {
    $form['upload_users_csv'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Upload Users CSV'),
      '#weight' => '1',
      '#attributes' => ['class' => ['upload-users-file']],
      '#prefix' => '<div class="upload-users-file">',
      '#suffix' => '<span class="error-message"></span></div>',
      '#upload_validators' => [
        'file_validate_extensions' => ['csv'],
      ],
      //'#upload_location' => 'private://society_users',
      '#ajax' => [
        'callback' => [$this, 'validateUsersFileMethod'],
        'progress' => ['type' => 'none', 'message' => NULL],
      ],
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

    
  public function validateUsersFileMethod() {
    $response = new AjaxResponse();
    $validators = ['file_validate_extensions' => ['csv']];
    $file = file_save_upload('upload_users_csv', $validators, FALSE, 0);
    if (!$file) {
      $response->addCommand(new InvokeCommand('.upload-users-file', 'addClass', array('error')));
      $response->addCommand(new InvokeCommand('.upload-users-file .error-message', 'attr', ['value', 'This should be a CSV']));
      return $ajax_response;
    }

  // The rest of my submit function. 
  }
  
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_users_file = $form_state->getValue('upload_users_csv', 0);
    if (isset($form_users_file[0]) && !empty($form_users_file[0])) {
      $users_file = File::load($form_users_file[0]);
      $users_file->setPermanent();
      $users_file->save();
      $destination = $users_file->get('uri')->value;
      $users_data_file = fopen($destination, "r");
      $users_data = [];
      $row = 0;
      while (!feof($users_data_file)) {
        $users_data[$row] = fgetcsv($users_data_file);
        $row++;
      }
      // Unset header row.
      unset($users_data[0]);
    }
    
    $sid = $form_state->getValue('group_id');
    //kint($task_date);exit;
    
    // Save Users.
    $save_users = UserGroupTasksCoreService::saveUsersOfGroup($users_data, $sid);
    if (is_bool($save_users) && $save_users) {
      \Drupal::messenger()->addMessage('Saved Users Successfully');
    }
    else if (is_array($save_users)){
      \Drupal::messenger()->addMessage('Uh oh! There is an issue with the below data you uploaded. Please check!');
      foreach ($save_users as $error_msg) {
        \Drupal::messenger()->addMessage($error_msg);
      }
    }
  }

}
