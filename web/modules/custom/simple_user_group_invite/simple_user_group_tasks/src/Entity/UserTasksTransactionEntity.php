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
 * Defines the User Tasks Transaction entity.
 *
 * @ingroup simple_user_group_tasks
 *
 * @ContentEntityType(
 *   id = "user_tasks_transaction",
 *   label = @Translation("User Tasks Transaction"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\simple_user_group_tasks\UserTasksTransactionEntityListBuilder",
 *     "views_data" = "Drupal\simple_user_group_tasks\Entity\UserTasksTransactionEntityViewsData",
 *     "translation" = "Drupal\simple_user_group_tasks\UserTasksTransactionEntityTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\simple_user_group_tasks\Form\UserTasksTransactionEntityForm",
 *       "add" = "Drupal\simple_user_group_tasks\Form\UserTasksTransactionEntityForm",
 *       "edit" = "Drupal\simple_user_group_tasks\Form\UserTasksTransactionEntityForm",
 *       "delete" = "Drupal\simple_user_group_tasks\Form\UserTasksTransactionEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\simple_user_group_tasks\UserTasksTransactionEntityHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\simple_user_group_tasks\UserTasksTransactionEntityAccessControlHandler",
 *   },
 *   base_table = "user_tasks_transaction",
 *   data_table = "user_tasks_transaction_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer user tasks transaction entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/user_tasks_transaction/{user_tasks_transaction}",
 *     "add-form" = "/admin/structure/user_tasks_transaction/add",
 *     "edit-form" = "/admin/structure/user_tasks_transaction/{user_tasks_transaction}/edit",
 *     "delete-form" = "/admin/structure/user_tasks_transaction/{user_tasks_transaction}/delete",
 *     "collection" = "/admin/structure/user_tasks_transaction",
 *   },
 *   field_ui_base_route = "user_tasks_transaction.settings"
 * )
 */
class UserTasksTransactionEntity extends ContentEntityBase implements UserTasksTransactionEntityInterface {

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
      ->setDescription(t('The user ID of author of the User Tasks Transaction entity.'))
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
    
    $fields['user_task_transaction_user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('User linked to the task'))
      ->setDescription(t('User linked to the task.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
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
    
    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the User Tasks Transaction entity.'))
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
    
    $fields['task_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Associated Task'))
      ->setDescription(t('The task ID to which the user points is to be mapped.'))
      ->setSetting('target_type', 'user_tasks')
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
    
    $fields['task_stake_points'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('User points at stake'))
      ->setDescription(t('User points at stake'))
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
    
    $fields['task_done_points'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('User points gained on completing task'))
      ->setDescription(t('User points gained on completing task'))
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
    
    $fields['task_status'] = BaseFieldDefinition::create('list_integer')
      ->setLabel(t('User activity status'))
      ->setDescription(t('The current status of activity user picked.'))
      ->setDefaultValue(0)
      ->setRevisionable(TRUE)
      ->setSettings([
        'allowed_values' => [
          0 => 'Assigned',
          2 => 'Done',
          3 => 'Expired'
        ],
      ])
      ->setDisplayOptions('view', [
        'label' => 'visible',
        'type' => 'list_default',
        'weight' => 6,
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 6,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['status']->setDescription(t('A boolean indicating whether the User Tasks Transaction is published.'))
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
