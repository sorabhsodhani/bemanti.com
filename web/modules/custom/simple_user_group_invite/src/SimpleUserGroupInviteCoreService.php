<?php

namespace Drupal\simple_user_group_invite;

use Drupal\simple_user_group_invite\Entity\UserGroupInviteEntity;
use Drupal\user\Entity\User;

/**
 * Class SimpleUserGroupInviteCoreService.
 */
class SimpleUserGroupInviteCoreService {

  /**
   * Constructs a new SimpleUserGroupInviteCoreService object.
   */
  public function __construct() {

  }

  public static function saveUserGroupInviteEntity ($user_groups = [], $emails = [], $groupId = NULL, $status = 0) {
    $account = \Drupal::currentUser();
    $user_data = User::load($account->id());
    $results = [];
    foreach ($user_groups as $user_group) {
      foreach ($emails as $email) {
        $ids = \Drupal::entityQuery('user')
          ->condition('mail', $email)
          ->execute();
        if (!empty($ids)) {
          $user_id = reset($ids);
        }
        else {
          $mailManager = \Drupal::service('plugin.manager.mail');
          $module = 'simple_user_group_invite';
          $key = 'invite_user';
          $to = $email;
          $params['invited_by'] = \Drupal::currentUser()->getEmail();
          $langcode = \Drupal::currentUser()->getPreferredLangcode();
          $send = true;

          $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
        }
        $inviteEntity = UserGroupInviteEntity::create([
          'name' => $email,
          'label' => $email,
          'email' => $email,
          'invited_for_group' => $user_group,
          'invited_by' => $user_data->id(),
          'invite_status' => $status,
        ]);
        if ($inviteEntity->save()) {
          $results[] = TRUE;
        }
        else {
          $results[] = FALSE;
        }
      }
    }
    return $results;
  }

}
