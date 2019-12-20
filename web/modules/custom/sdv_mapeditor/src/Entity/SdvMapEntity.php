<?php

namespace Drupal\sdv_mapeditor\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Map entity.
 *
 * @ingroup sdv_mapeditor
 *
 * @ContentEntityType(
 *   id = "sdv_map",
 *   label = @Translation("Map"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\sdv_mapeditor\SdvMapEntityListBuilder",
 *     "views_data" = "Drupal\sdv_mapeditor\Entity\SdvMapEntityViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\sdv_mapeditor\Form\SdvMapEntityForm",
 *       "add" = "Drupal\sdv_mapeditor\Form\SdvMapEntityForm",
 *       "edit" = "Drupal\sdv_mapeditor\Form\SdvMapEntityForm",
 *       "delete" = "Drupal\sdv_mapeditor\Form\SdvMapEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\sdv_mapeditor\SdvMapEntityHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\sdv_mapeditor\SdvMapEntityAccessControlHandler",
 *   },
 *   base_table = "sdv_map",
 *   translatable = FALSE,
 *   admin_permission = "administer map entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/sdv_map/{sdv_map}",
 *     "add-form" = "/admin/structure/sdv_map/add",
 *     "edit-form" = "/admin/structure/sdv_map/{sdv_map}/edit",
 *     "delete-form" = "/admin/structure/sdv_map/{sdv_map}/delete",
 *     "collection" = "/admin/structure/sdv_map",
 *   },
 *   field_ui_base_route = "sdv_map.settings"
 * )
 */
class SdvMapEntity extends ContentEntityBase implements SdvMapEntityInterface {

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
  public function getStatus() {
    return $this->get('status')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->get('description')->value;
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
  public function getChangedTime() {
    return $this->get('changed')->value;
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
  public function getParameters() {
    $parameters = '';
    $body = $this->get('gis_ia_params')->value;
    $body = preg_replace("/\r\n/", "\r", $body);
    $body = preg_replace("/\n/", "\r", $body);
    $body = explode("\r", $body);
    foreach ($body as $b) {
      if (substr($b, 0, 2) == 'm=') {
        $b = 'm=' . base64_encode(htmlspecialchars(trim(substr($b, 2))));
      }
      $parameters .= ($parameters == '' ? '' : ',') . $b;
      if (substr($b, 0, 2) == 'u=') {

        // @todo fin out where this is used.
        $uiterlijk = substr($b, 2);
      }
    }
    return $parameters;
  }

  /**
   * {@inheritDoc}
   */
  public function getAppearance() {
    $uiterlijk = 'groen';
    $body = $this->get('gis_ia_params')->value;
    $body = preg_replace("/\r\n/", "\r", $body);
    $body = preg_replace("/\n/", "\r", $body);
    $body = explode("\r", $body);
    foreach ($body as $b) {
      if (substr($b, 0, 2) == 'u=') {
        $uiterlijk = substr($b, 2);
      }
    }
    return $uiterlijk;
  }

  /**
   * {@inheritDoc}
   */
  public function getLayers() {
    if ($this->get('gis_ia_layers')->value) {
      $layers = ',ld=';
      $ldefs = str_replace("\r\n", "\r", $this->get('gis_ia_layers')->value);
      $ldefs = str_replace("\n", "\r", $ldefs);
      $ldefs = explode("\r", $ldefs);
      foreach ($ldefs as $ldef) {
        if ($ldef != '') {
          $layers .= base64_encode($ldef) . '|';
        }
      }
      return $layers;
    }
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
      ->setDescription(t('The user ID of author of the map.'))
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
      ->setDescription(t('The name of the map. Will be used in admin overview.'))
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
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

    $fields['description'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Description'))
      ->setDescription(t('Description of the map. Will be used in admin overview.'))
      ->setSettings([
        'max_length' => 120,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -2,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -2,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['status']->setDescription(t('A boolean indicating whether the map is published.'))
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

    $fields['gis_ia_params'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('GIS IA parameters'))
      ->setDescription(t('This field will hold the serialized parameters for the GIS IA'))
      ->setRevisionable(TRUE)
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => 10,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textarea',
        'settings' => [
          'rows' => 12,
        ],
        'weight' => 10,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(FALSE);

    $fields['gis_ia_layers'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('GIS IA layers'))
      ->setDescription(t('This field will hold the serialized layer definitions for the GIS IA'))
      ->setRevisionable(TRUE)
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => 12,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textarea',
        'settings' => [
          'rows' => 12,
        ],
        'weight' => 12,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(FALSE);

    return $fields;
  }

}
