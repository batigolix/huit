<?php

namespace Drupal\sdv_usabilla\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Extension\ThemeHandler;

/**
 * Defines the Usabilla item entity.
 *
 * @ingroup sdv_usabilla
 *
 * @ContentEntityType(
 *   id = "usabilla_item",
 *   label = @Translation("Usabilla item"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\sdv_usabilla\UsabillaItemEntityListBuilder",
 *     "views_data" = "Drupal\sdv_usabilla\Entity\UsabillaItemEntityViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\sdv_usabilla\Form\UsabillaItemEntityForm",
 *       "add" = "Drupal\sdv_usabilla\Form\UsabillaItemEntityForm",
 *       "edit" = "Drupal\sdv_usabilla\Form\UsabillaItemEntityForm",
 *       "delete" = "Drupal\sdv_usabilla\Form\UsabillaItemEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\sdv_usabilla\UsabillaItemEntityHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\sdv_usabilla\UsabillaItemEntityAccessControlHandler",
 *   },
 *   base_table = "usabilla_item",
 *   translatable = FALSE,
 *   admin_permission = "administer usabilla item entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/content/usabilla_item/{usabilla_item}",
 *     "add-form" = "/admin/content/usabilla_item/add",
 *     "edit-form" = "/admin/content/usabilla_item/{usabilla_item}/edit",
 *     "delete-form" = "/admin/content/usabilla_item/{usabilla_item}/delete",
 *     "collection" = "/admin/content/usabilla_item",
 *   },
 *   field_ui_base_route = "usabilla_item.settings"
 * )
 */
class UsabillaItemEntity extends ContentEntityBase implements UsabillaItemEntityInterface {

  use EntityChangedTrait;
  use EntityPublishedTrait;

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getUsabillaId() {
    return $this->get('usabilla_id')->value;
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
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Add the published field.
    $fields += static::publishedBaseFieldDefinitions($entity_type);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Usabilla item entity.'))
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
      ->setDescription(t('Description of the Usabilla item. Will be used in admin overview.'))
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

    $fields['usabilla_id'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Usabilla ID'))
      ->setDescription(t('The ID of the item as defined by Usabilla. This can be found in the embed code.'))
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

    // Provides the Usabilla type field.
    $fields['type'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Type'))
      ->setDescription(t('The Usabilla item type: <a href="@button_url">Feedback button</a> or <a href="@widget_url">In-page widget</a>. <strong>Note: in-page widget is not implemented yet</strong>.', ['@button_url'=>'https://developers.usabilla.com/#feedback-button', '@widget_url'=> 'https://developers.usabilla.com/#in-page']))
      ->setSettings(array(
        'allowed_values' => array(
          'button' => t('Feedback button'),
          'widget' => t('In-page widget'),
        ),
      ))
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'list_default',
        'weight' => -4,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'options_select',
        'weight' => -4,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // Provides the theme field.
    $fields['theme'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Theme'))
      ->setDescription(t('Choose in which theme the Usabilla item should be enabled.'))
      ->setSettings(array(
        'allowed_values_function' => 'sdv_usabilla_themes',
      ))
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'list_default',
        'weight' => -4,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'options_select',
        'weight' => -4,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status']->setDescription(t('A boolean indicating whether the Usabilla item is published.'))
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
