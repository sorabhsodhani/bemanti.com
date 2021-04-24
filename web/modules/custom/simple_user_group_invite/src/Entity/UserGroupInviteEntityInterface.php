<?php

namespace Drupal\simple_user_group_invite\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining User Group Invites entities.
 *
 * @ingroup simple_user_group_invite
 */
interface UserGroupInviteEntityInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the User Group Invites name.
   *
   * @return string
   *   Name of the User Group Invites.
   */
  public function getName();

  /**
   * Sets the User Group Invites name.
   *
   * @param string $name
   *   The User Group Invites name.
   *
   * @return \Drupal\simple_user_group_invite\Entity\UserGroupInviteEntityInterface
   *   The called User Group Invites entity.
   */
  public function setName($name);

  /**
   * Gets the User Group Invites creation timestamp.
   *
   * @return int
   *   Creation timestamp of the User Group Invites.
   */
  public function getCreatedTime();

  /**
   * Sets the User Group Invites creation timestamp.
   *
   * @param int $timestamp
   *   The User Group Invites creation timestamp.
   *
   * @return \Drupal\simple_user_group_invite\Entity\UserGroupInviteEntityInterface
   *   The called User Group Invites entity.
   */
  public function setCreatedTime($timestamp);

}
