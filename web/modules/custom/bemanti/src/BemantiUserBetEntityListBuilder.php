<?php

namespace Drupal\bemanti;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Bemanti user bet entity entities.
 *
 * @ingroup bemanti
 */
class BemantiUserBetEntityListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Bemanti user bet entity ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\bemanti\Entity\BemantiUserBetEntity $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.bemanti_user_bet_entity.edit_form',
      ['bemanti_user_bet_entity' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
