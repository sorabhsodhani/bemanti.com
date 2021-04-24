<?php

namespace Drupal\bemanti\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Bemanti user bet entity entity.
 *
 * @ingroup bemanti
 *
 * @ContentEntityType(
 *   id = "bemanti_user_bet_entity",
 *   label = @Translation("Bemanti user bet entity"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\bemanti\BemantiUserBetEntityListBuilder",
 *     "views_data" = "Drupal\bemanti\Entity\BemantiUserBetEntityViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\bemanti\Form\BemantiUserBetEntityForm",
 *       "add" = "Drupal\bemanti\Form\BemantiUserBetEntityForm",
 *       "edit" = "Drupal\bemanti\Form\BemantiUserBetEntityForm",
 *       "delete" = "Drupal\bemanti\Form\BemantiUserBetEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\bemanti\BemantiUserBetEntityHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\bemanti\BemantiUserBetEntityAccessControlHandler",
 *   },
 *   base_table = "bemanti_user_bet_entity",
 *   translatable = FALSE,
 *   admin_permission = "administer bemanti user bet entity entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/bemanti_user_bet_entity/{bemanti_user_bet_entity}",
 *     "add-form" = "/admin/structure/bemanti_user_bet_entity/add",
 *     "edit-form" = "/admin/structure/bemanti_user_bet_entity/{bemanti_user_bet_entity}/edit",
 *     "delete-form" = "/admin/structure/bemanti_user_bet_entity/{bemanti_user_bet_entity}/delete",
 *     "collection" = "/admin/structure/bemanti_user_bet_entity",
 *   },
 *   field_ui_base_route = "bemanti_user_bet_entity.settings"
 * )
 */
class BemantiUserBetEntity extends ContentEntityBase implements BemantiUserBetEntityInterface {

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
      ->setDescription(t('The user ID of author of the Bemanti user bet entity entity.'))
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
      ->setDescription(t('The name of the Bemanti user bet entity entity.'))
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
    $fields['bemanti_user_bet_user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('User linked to the bet'))
      ->setDescription(t('User linked to the bet.'))
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
    $fields['bemanti_user_bet_matchid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Associated Match'))
      ->setDescription(t('The Match ID to which the user points is to be mapped.'))
      ->setSetting('target_type', 'node')
      ->setSetting('handler', 'default')
      ->setSetting('handler_settings', [
        'target_bundles' => ['sl_match' => 'sl_match'],
        'auto_create' => FALSE,
      ])
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
    $fields['bemanti_user_bet_teamid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Associated Team'))
      ->setDescription(t('The Team ID to which the user points is to be mapped.'))
      ->setSetting('target_type', 'node')
      ->setSetting('handler', 'default')
      ->setSetting('handler_settings', [
        'target_bundles' => ['sl_team' => 'sl_team'],
        'auto_create' => FALSE,
      ])
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
    $fields['bemanti_user_stake_points'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('User points at stake'))
      ->setDescription(t('User points at stake'))
      ->setDefaultValue(0)
      ->setSettings(array(
        'precision' => 10,
        'scale' => 4,
      ))
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'number_decimal',
        'weight' => 4,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'number',
        'weight' => -4,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
    $fields['bemanti_user_bet_effecting_points'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('User points that can be gained'))
      ->setDescription(t('User points that can be gained'))
      ->setDefaultValue(0)
      ->setSettings(array(
        'precision' => 10,
        'scale' => 4,
      ))
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'number_decimal',
        'weight' => 5,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'number',
        'weight' => -4,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
    $fields['status']->setDescription(t('A boolean indicating whether the Bemanti user bet entity is published.'))
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
