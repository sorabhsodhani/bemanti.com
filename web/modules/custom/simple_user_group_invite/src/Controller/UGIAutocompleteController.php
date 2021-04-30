<?php

namespace Drupal\simple_user_group_invite\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\simple_user_group_invite\Entity\UserGroupEntity;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Entity\Element\EntityAutocomplete;
use Drupal\Component\Utility\Xss;

/**
 * Class UGIAutocompleteController.
 */
class UGIAutocompleteController extends ControllerBase {

  /**
   * Usergroupsbyuserid.
   *
   * @return string
   *   Return Hello string.
   */
  public function usergroupsbyuserid(Request $request) {
    $results = [];
    $input = $request->query->get('q');
    if (!$input) {
      return new JsonResponse($results);
    }
    $input = Xss::filter($input);
    $account = \Drupal::currentUser();
    $user_data = User::load($account->id());
    $uid = $user_data->id();
    $query = \Drupal::entityQuery('user_group')->condition('user_id', $uid, '=');
    $results = $query->execute();
    $usergroupsList = [];
    foreach ($results as $result) {
      $groupData = UserGroupEntity::load($result);
      $usergroupsList[] = [
        'value' => EntityAutocomplete::getEntityLabels([$groupData]),
        'label' => $groupData->getName(),
      ];
    }
    return new JsonResponse($usergroupsList);
  }
  /**
   * Usersexcludingcurrentloggedin.
   *
   * @return string
   *   Return Hello string.
   */
  public function usersexcludingcurrentloggedin(Request $request) {
    $results = [];
    $input = $request->query->get('q');
    if (!$input) {
      return new JsonResponse($results);
    }
    $input = Xss::filter($input);
    $account = \Drupal::currentUser();
    $user_data = User::load($account->id());
    $uid = $user_data->id();
    $query = \Drupal::entityQuery('user')->condition('uid', $uid, '!=');
    $results = $query->execute();
    $usersList = [];
    foreach ($results as $result) {
      $userData = User::load($result);
      $usersList[] = [
        'value' => EntityAutocomplete::getEntityLabels([$userData]),
        'label' => $userData->getUsername(),
      ];
    }
    return new JsonResponse($usersList);
  }

}
