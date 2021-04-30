<?php

namespace Drupal\simple_user_group_tasks\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining User Tasks Transaction entities.
 *
 * @ingroup simple_user_group_tasks
 */
interface UserTasksTransactionEntityInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the User Tasks Transaction name.
   *
   * @return string
   *   Name of the User Tasks Transaction.
   */
  public function getName();

  /**
   * Sets the User Tasks Transaction name.
   *
   * @param string $name
   *   The User Tasks Transaction name.
   *
   * @return \Drupal\simple_user_group_tasks\Entity\UserTasksTransactionEntityInterface
   *   The called User Tasks Transaction entity.
   */
  public function setName($name);

  /**
   * Gets the User Tasks Transaction creation timestamp.
   *
   * @return int
   *   Creation timestamp of the User Tasks Transaction.
   */
  public function getCreatedTime();

  /**
   * Sets the User Tasks Transaction creation timestamp.
   *
   * @param int $timestamp
   *   The User Tasks Transaction creation timestamp.
   *
   * @return \Drupal\simple_user_group_tasks\Entity\UserTasksTransactionEntityInterface
   *   The called User Tasks Transaction entity.
   */
  public function setCreatedTime($timestamp);

}
