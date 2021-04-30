<?php

namespace Drupal\simple_user_group_tasks\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the User Tasks[D[D[D[D[DGroup Tasks entity.
 *
 * @ingroup simple_user_group_tasks
 *
 * @ContentEntityType(
 *   id = "user_tasks",
 *   label = @Translation("User Group Tasks"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\simple_user_group_tasks\UserTasksEntityListBuilder",
 *     "views_data" = "Drupal\simple_user_group_tasks\Entity\UserTasksEntityViewsData",
 *     "translation" = "Drupal\simple_user_group_tasks\UserTasksEntityTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\simple_user_group_tasks\Form\UserTasksEntityForm",
 *       "add" = "Drupal\simple_user_group_tasks\Form\UserTasksEntityForm",
 *       "edit" = "Drupal\simple_user_group_tasks\Form\UserTasksEntityForm",
 *       "delete" = "Drupal\simple_user_group_tasks\Form\UserTasksEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\simple_user_group_tasks\UserTasksEntityHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\simple_user_group_tasks\UserTasksEntityAccessControlHandler",
 *   },
 *   base_table = "user_tasks",
 *   data_table = "user_tasks_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer user group tasks entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/user_tasks/{user_tasks}",
 *     "add-form" = "/admin/structure/user_tasks/add",
 *     "edit-form" = "/admin/structure/user_tasks/{user_tasks}/edit",
 *     "delete-form" = "/admin/structure/user_tasks/{user_tasks}/delete",
 *     "collection" = "/admin/structure/user_tasks",
 *   },
 *   field_ui_base_route = "user_tasks.settings"
 * )
 */
class UserTasksEntity extends ContentEntityBase implements UserTasksEntityInterface {

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
      ->setDescription(t('The user ID of author of the User Tasks[D[D[D[D[DGroup Tasks entity.'))
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

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the User Tasks[D[D[D[D[DGroup Tasks entity.'))
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
    
    $fields['user_group_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Associated User Group'))
      ->setDescription(t('The group ID to which the task is to be mapped.'))
      ->setSetting('target_type', 'user_group')
      ->setSetting('handler', 'default')
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
    
    $fields['user_group_task_pointfactor'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Point Factor for the task'))
      ->setDescription(t('The point factor for the activity'))
      ->setDefaultValue(0)
      ->setSettings(array(
        'precision' => 15,
        'scale' => 5,
      ))
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'number_decimal',
        'weight' => -5,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
    
    $fields['task_start_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Task Date'))
      ->setDescription(t('Task Date'))
      ->setSettings([
        'datetime_type' => 'date',
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'datetime_default',
        'settings' => [
          'format_type' => 'medium',
        ],
        'weight' => -9,
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_default',
        'weight' => -9,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
    

    $fields['status']->setDescription(t('A boolean indicating whether the User Tasks[D[D[D[D[DGroup Tasks is published.'))
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
