<?php

namespace Drupal\simple_user_group_points\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the User Group points entity.
 *
 * @ingroup simple_user_group_points
 *
 * @ContentEntityType(
 *   id = "user_group_points",
 *   label = @Translation("User Group points"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\simple_user_group_points\UserGroupPointsEntityListBuilder",
 *     "views_data" = "Drupal\simple_user_group_points\Entity\UserGroupPointsEntityViewsData",
 *     "translation" = "Drupal\simple_user_group_points\UserGroupPointsEntityTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\simple_user_group_points\Form\UserGroupPointsEntityForm",
 *       "add" = "Drupal\simple_user_group_points\Form\UserGroupPointsEntityForm",
 *       "edit" = "Drupal\simple_user_group_points\Form\UserGroupPointsEntityForm",
 *       "delete" = "Drupal\simple_user_group_points\Form\UserGroupPointsEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\simple_user_group_points\UserGroupPointsEntityHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\simple_user_group_points\UserGroupPointsEntityAccessControlHandler",
 *   },
 *   base_table = "user_group_points",
 *   data_table = "user_group_points_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer user group points entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/user_group_points/{user_group_points}",
 *     "add-form" = "/admin/structure/user_group_points/add",
 *     "edit-form" = "/admin/structure/user_group_points/{user_group_points}/edit",
 *     "delete-form" = "/admin/structure/user_group_points/{user_group_points}/delete",
 *     "collection" = "/admin/structure/user_group_points",
 *   },
 *   field_ui_base_route = "user_group_points.settings"
 * )
 */
class UserGroupPointsEntity extends ContentEntityBase implements UserGroupPointsEntityInterface {

  use EntityChangedTrait;
  use EntityPublishedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Add the published field.
    $fields += static::publishedBaseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the User Group points entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
    $fields['user_group_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Associated Group'))
      ->setDescription(t('The Group ID to which the user points is to be mapped.'))
      ->setSetting('target_type', 'user_group')
      ->setSetting('handler', 'default')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => -5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the User Group points entity.'))
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);
    $fields['user_group_points_amount'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Points that has to be added or deducted'))
      ->setDescription(t('Points that has to be added or deducted'))
      ->setDefaultValue(0)
      ->setSettings(array(
        'precision' => 10,
        'scale' => 2,
      ))
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'number_decimal',
        'weight' => -5,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'number',
        'weight' => -4,
      ))
      ->setRevisionable(TRUE)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['user_group_points_balance'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Balance Points at this transaction'))
      ->setDescription(t('Balance Points at this transaction'))
      ->setDefaultValue(0)
      ->setSettings(array(
        'precision' => 10,
        'scale' => 2,
      ))
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'number_decimal',
        'weight' => -4,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'number',
        'weight' => -4,
      ))
      ->setRevisionable(TRUE)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status']->setDescription(t('A boolean indicating whether the User Group points is published.'))
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -3,
      ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
