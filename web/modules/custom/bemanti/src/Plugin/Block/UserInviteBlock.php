<?php

namespace Drupal\bemanti\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormInterface;

/**
 * Provides a 'UserInviteBlock' block.
 *
 * @Block(
 *  id = "user_invite_block",
 *  admin_label = @Translation("User invite block"),
 * )
 */
class UserInviteBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\bemanti\Form\UserGroupInviteForm');

    return $form;
  }

}
