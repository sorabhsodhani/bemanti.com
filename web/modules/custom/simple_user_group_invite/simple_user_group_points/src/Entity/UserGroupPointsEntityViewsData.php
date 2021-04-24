<?php

namespace Drupal\simple_user_group_points\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for User Group points entities.
 */
class UserGroupPointsEntityViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }

}
