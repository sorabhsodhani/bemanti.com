<?php

namespace Drupal\simple_user_group_tasks\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\FileInterface;

/**
 * Class TestForm.
 */
class TestForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'test_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
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
//    $all_files = $this->getRequest()->files->get('files', []);
//    if (!empty($all_files['uploadyourfile'])) {
//      $file_upload = $all_files['uploadyourfile'];
//      if ($file_upload->isValid()) {
//        $form_state->setValue('uploadyourfile', $file_upload->getRealPath());
//        return;
//      }
//    }
//
//    $form_state->setErrorByName('uploadyourfile', $this->t('The file could not be uploaded.'));
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Display result.
//    $validators = ['file_validate_extensions' => ['csv']];
//    $file = file_save_upload('uploadyourfile', $validators, FALSE, 0);
//    if (!$file) {
//      return;
//    }
  }
  
  /**
 * Add a custom validator to file_upload.
 *
 * @param \Drupal\file\FileInterface $file
 *   A file entity.
 * @param mixed $first_parameter
 *   (optional) Some additional data.
 *
 * @return array
 *   An empty array if the file size is below limits or an array containing an
 *   error message if it's not.
 *
 * @see hook_file_validate()
 */
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
}
