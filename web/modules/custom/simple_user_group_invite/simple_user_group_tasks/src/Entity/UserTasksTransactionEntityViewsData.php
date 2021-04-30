<?php

namespace Drupal\simple_user_group_tasks\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for User Tasks Transaction entities.
 */
class UserTasksTransactionEntityViewsData extends EntityViewsData {

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
