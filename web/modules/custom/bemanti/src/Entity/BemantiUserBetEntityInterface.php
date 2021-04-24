<?php

namespace Drupal\bemanti\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Bemanti user bet entity entities.
 *
 * @ingroup bemanti
 */
interface BemantiUserBetEntityInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Bemanti user bet entity name.
   *
   * @return string
   *   Name of the Bemanti user bet entity.
   */
  public function getName();

  /**
   * Sets the Bemanti user bet entity name.
   *
   * @param string $name
   *   The Bemanti user bet entity name.
   *
   * @return \Drupal\bemanti\Entity\BemantiUserBetEntityInterface
   *   The called Bemanti user bet entity entity.
   */
  public function setName($name);

  /**
   * Gets the Bemanti user bet entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Bemanti user bet entity.
   */
  public function getCreatedTime();

  /**
   * Sets the Bemanti user bet entity creation timestamp.
   *
   * @param int $timestamp
   *   The Bemanti user bet entity creation timestamp.
   *
   * @return \Drupal\bemanti\Entity\BemantiUserBetEntityInterface
   *   The called Bemanti user bet entity entity.
   */
  public function setCreatedTime($timestamp);

}
