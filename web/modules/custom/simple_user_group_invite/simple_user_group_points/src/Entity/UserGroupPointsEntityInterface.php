<?php

namespace Drupal\simple_user_group_points\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining User Group points entities.
 *
 * @ingroup simple_user_group_points
 */
interface UserGroupPointsEntityInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the User Group points name.
   *
   * @return string
   *   Name of the User Group points.
   */
  public function getName();

  /**
   * Sets the User Group points name.
   *
   * @param string $name
   *   The User Group points name.
   *
   * @return \Drupal\simple_user_group_points\Entity\UserGroupPointsEntityInterface
   *   The called User Group points entity.
   */
  public function setName($name);

  /**
   * Gets the User Group points creation timestamp.
   *
   * @return int
   *   Creation timestamp of the User Group points.
   */
  public function getCreatedTime();

  /**
   * Sets the User Group points creation timestamp.
   *
   * @param int $timestamp
   *   The User Group points creation timestamp.
   *
   * @return \Drupal\simple_user_group_points\Entity\UserGroupPointsEntityInterface
   *   The called User Group points entity.
   */
  public function setCreatedTime($timestamp);

}
