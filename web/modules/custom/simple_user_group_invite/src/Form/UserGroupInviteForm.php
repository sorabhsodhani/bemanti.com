<?php

namespace Drupal\simple_user_group_invite\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;
use Drupal\simple_user_group_invite\SimpleUserGroupInviteCoreService;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AlertCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\HtmlCommand;

/**
 * Class UserGroupInviteForm.
 */
class UserGroupInviteForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'simple_user_grp_invite_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $account = \Drupal::currentUser();
    $user_data = User::load($account->id());
    $uid = $user_data->id();
    $form['invite_gangs'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'invite_gangs'],
    ];
    $form['invite_gangs']['user_group'] = [
      '#title' => $this->t('Select your group'),
      '#type' => 'textfield',
      '#weight' => '0',
      '#autocomplete_route_name' => 'simple_user_group_invite.ugi_autocomplete_usergroupsbyuserid'
    ];
    $form['invite_gangs']['username'] = [
      '#title' => $this->t('Select user names'),
      '#type' => 'textfield',
      '#weight' => '0',
      '#autocomplete_route_name' => 'simple_user_group_invite.ugi_autocomplete_usersexcludingcurrentloggedin'
    ];
    $form['invite_gangs']['list_of_email_addresses'] = [
      '#type' => 'textarea',
      '#title' => $this->t('List of email addresses'),
      '#weight' => '0',
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#ajax' => [
        'callback' => '::sendInvite', // don't forget :: when calling a class method.
        'event' => 'click',
        'wrapper' => 'invite_gangs', // This element is updated with this AJAX callback.
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Sending Invite...'),
        ],
      ]
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
    // Display result.

  }

  public function sendInvite (array &$form,  FormStateInterface &$form_state) {
    $ajax_response = new AjaxResponse();
    $email_addresses = ($form_state->getValue('list_of_email_addresses') != '') ? explode(',', $form_state->getValue('list_of_email_addresses')) : [];
    $form_state_user_name = $form_state->getValue('username');
    
    $user_names = (is_array($form_state_user_name)) ? $form_state_user_name : 
      (($form_state_user_name == '') ? [] : [$form_state_user_name]);
    $user_groups = [];
    $user_group_inputs = (is_array($form_state->getValue('user_group'))) ? $form_state->getValue('user_group') : [$form_state->getValue('user_group')];
    
    $invite_emails = [];
    foreach ($user_group_inputs as $user_group_input) {
      preg_match_all('#\((.*?)\)#', $user_group_input, $user_group_match_ids);
      $user_groups[] = end(end($user_group_match_ids));
    }
    \Drupal::logger('form_input')->warning('<pre><code>'. print_r($user_groups, TRUE) .'</code></pre>');
    foreach ($user_names as $user_name_input) {
      preg_match_all('#\((.*?)\)#', $user_name_input, $user_name_match_ids);
      $user_data = User::load(end(end($user_name_match_ids)));
      $temp_email = $user_data->getEmail();
      if ($temp_email != NULL) {
        $invite_emails[] = $temp_email;
      }
    }
    $invite_emails = array_merge($invite_emails, $email_addresses);
    $responses = SimpleUserGroupInviteCoreService::saveUserGroupInviteEntity($user_groups, $invite_emails);
    foreach ($responses as $response) {
      if (!$response) {
        $ajax_response->addCommand(new AlertCommand('Some issue with one of the invite!'));
      }
      else {
        $ajax_response->addCommand(new AlertCommand('Invites sent Successfully!'));
      }
    }
    return $ajax_response;
  }

}
