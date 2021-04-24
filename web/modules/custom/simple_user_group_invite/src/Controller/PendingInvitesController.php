<?php

namespace Drupal\simple_user_group_invite\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;
use Drupal\node\Entity\Node;
use Drupal\Core\Render\Markup;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Render\Renderer;

/**
 * Class PendingInvitesController.
 */
class PendingInvitesController extends ControllerBase {

  /**
   * Pendinginvites.
   *
   * @return string
   *   Return Hello string.
   */
  public function pendingInvites() {
    $account = \Drupal::currentUser()->getAccount();

    $user_data = User::load($account->id());

    $invites_query = \Drupal::entityQuery('user_group_invite')->condition('email', $user_data->getEmail());

    $invites_list = $invites_query->execute();

    foreach ($invites_list as $key => $id) {
      $build[] =  \Drupal::formBuilder()->getForm('Drupal\simple_user_group_invite\Form\InviteAcceptForm', $id);
    }

    $build['#cache']['max-age'] = 0;
    \Drupal::service('page_cache_kill_switch')->trigger();

     return $build;
  }

}
