<?php

namespace Drupal\simple_user_group_invite\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining User Groups entities.
 *
 * @ingroup simple_user_group_invite
 */
interface UserGroupEntityInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the User Groups name.
   *
   * @return string
   *   Name of the User Groups.
   */
  public function getName();

  /**
   * Sets the User Groups name.
   *
   * @param string $name
   *   The User Groups name.
   *
   * @return \Drupal\simple_user_group_invite\Entity\UserGroupEntityInterface
   *   The called User Groups entity.
   */
  public function setName($name);

  /**
   * Gets the User Groups creation timestamp.
   *
   * @return int
   *   Creation timestamp of the User Groups.
   */
  public function getCreatedTime();

  /**
   * Sets the User Groups creation timestamp.
   *
   * @param int $timestamp
   *   The User Groups creation timestamp.
   *
   * @return \Drupal\simple_user_group_invite\Entity\UserGroupEntityInterface
   *   The called User Groups entity.
   */
  public function setCreatedTime($timestamp);

}
