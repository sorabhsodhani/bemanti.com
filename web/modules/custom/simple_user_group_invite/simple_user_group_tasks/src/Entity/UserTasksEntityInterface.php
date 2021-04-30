<?php

namespace Drupal\simple_user_group_tasks\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining User Tasks[D[D[D[D[DGroup Tasks entities.
 *
 * @ingroup simple_user_group_tasks
 */
interface UserTasksEntityInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the User Tasks[D[D[D[D[DGroup Tasks name.
   *
   * @return string
   *   Name of the User Tasks[D[D[D[D[DGroup Tasks.
   */
  public function getName();

  /**
   * Sets the User Tasks[D[D[D[D[DGroup Tasks name.
   *
   * @param string $name
   *   The User Tasks[D[D[D[D[DGroup Tasks name.
   *
   * @return \Drupal\simple_user_group_tasks\Entity\UserTasksEntityInterface
   *   The called User Tasks[D[D[D[D[DGroup Tasks entity.
   */
  public function setName($name);

  /**
   * Gets the User Tasks[D[D[D[D[DGroup Tasks creation timestamp.
   *
   * @return int
   *   Creation timestamp of the User Tasks[D[D[D[D[DGroup Tasks.
   */
  public function getCreatedTime();

  /**
   * Sets the User Tasks[D[D[D[D[DGroup Tasks creation timestamp.
   *
   * @param int $timestamp
   *   The User Tasks[D[D[D[D[DGroup Tasks creation timestamp.
   *
   * @return \Drupal\simple_user_group_tasks\Entity\UserTasksEntityInterface
   *   The called User Tasks[D[D[D[D[DGroup Tasks entity.
   */
  public function setCreatedTime($timestamp);

}
