<?php

namespace Drupal\simple_user_group_invite\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\simple_user_group_invite\Entity\UserGroupInviteEntity;
use Drupal\simple_user_group_invite\Entity\UserGroupEntity;
use Drupal\simple_user_group_points\SimpleUserGroupPointsService;
use Drupal\user\Entity\User;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AlertCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\HtmlCommand;

/**
 * Class InviteAcceptForm.
 */
class InviteAcceptForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    static $num = 0;
    $num++;
    return 'invite_accept_form_' . $num;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {
    if ($id != NULL) {
      $inviteData = UserGroupInviteEntity::load($id);
      $status = $inviteData->get('invite_status')->value;
      //print $status;exit;
      if ($inviteData != NULL && $status == 0) {
        $groupId_data = $inviteData->get('invited_for_group')->getValue();
        if (!empty($groupId_data)) {
          $form['#groupId'] = $groupId_data[0]['target_id'];
          $groupData = UserGroupEntity::load($groupId_data[0]['target_id']);
          $invite_wrapper = str_replace([':', '\\', '/', '*', 'X', ' ', "'"] , '_' , strtolower(trim($groupData->getName()))) . '_invite_wrapper';
          $form[$invite_wrapper] = [
            '#type' => 'container',
            '#attributes' => ['id' => $invite_wrapper],
          ];
          $form['#wrapper'] = $invite_wrapper;
          $form['#invite_id'] = $id;
          $form[$invite_wrapper]['submit'] = [
            '#type' => 'submit',
            '#value' => 'Accept',
            '#weight' => '0',
            '#prefix' => '<p>Do you want to join ' . $groupData->getName() . '?</p>',
            '#ajax' => [
              'callback' => '::acceptInvite', // don't forget :: when calling a class method.
              'event' => 'click',
              'wrapper' => $invite_wrapper, // This element is updated with this AJAX callback.
              'progress' => [
                'type' => 'throbber',
                'message' => $this->t('Accepting Invite...'),
              ],
            ]
          ];
          $form[$invite_wrapper]['decline'] = [
            '#type' => 'button',
            '#value' => 'Decline',
            '#weight' => '0',
            '#ajax' => [
              'callback' => '::declineInvite', // don't forget :: when calling a class method.
              'event' => 'click',
              'wrapper' => $invite_wrapper, // This element is updated with this AJAX callback.
              'progress' => [
                'type' => 'throbber',
                'message' => $this->t('Declining Invite...'),
              ],
            ]
          ];
        }
      }
    }


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
    // Display result.
    foreach ($form_state->getValues() as $key => $value) {
      \Drupal::messenger()->addMessage($key . ': ' . ($key === 'text_format'?$value['value']:$value));
    }
  }

  public function acceptInvite(array &$form,  FormStateInterface &$form_state) {
    $response = new AjaxResponse();
    $invite_wrapper = $form['#wrapper'];
    $invite_id = $form['#invite_id'];
    $group_id = $form['#groupId'];

    $account = \Drupal::currentUser();
    $user_data = User::load($account->id());
    $uid = $user_data->id();

    $inviteEntity = UserGroupInviteEntity::load($invite_id);
    $inviteEntity->set('invite_status', 1);

    if ($inviteEntity->save()) {
      $user_data->field_user_group[] = ['target_id' => $group_id];
      $user_data->save();
      $moduleHandler = \Drupal::service('module_handler');
      if ($moduleHandler->moduleExists('simple_user_group_points')) {
        $user_points_service = \Drupal::service("simple_user_group_points.default");
        $user_points_service->updateUserGroupPoints($user_data, $group_id, 'register');
      }
      $response->addCommand(new AlertCommand('Added to the group Successfully!'));
      $response->addCommand(new HtmlCommand('#' . $invite_wrapper, '<div></div>'));
    }
    // Return the AJAX response.
    return $response;
  }

    public function declineInvite(array &$form,  FormStateInterface &$form_state) {
    $response = new AjaxResponse();
    $invite_wrapper = $form['#wrapper'];
    $invite_id = $form['#invite_id'];
    $group_id = $form['#groupId'];

    $account = \Drupal::currentUser();
    $user_data = User::load($account->id());
    $uid = $user_data->id();

    $inviteEntity = UserGroupInviteEntity::load($invite_id);
    $inviteEntity->set('invite_status', 2);

    if ($inviteEntity->save()) {
      $response->addCommand(new AlertCommand('Declined Successfully!'));
      $response->addCommand(new HtmlCommand('#' . $invite_wrapper, '<div></div>'));
    }
    // Return the AJAX response.
    return $response;
  }

}
