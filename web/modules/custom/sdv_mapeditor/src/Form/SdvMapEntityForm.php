<?php

namespace Drupal\sdv_mapeditor\Form;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for Map edit forms.
 *
 * @ingroup sdv_mapeditor
 */
class SdvMapEntityForm extends ContentEntityForm {

  /**
   * The current user account.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $account;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    $instance = parent::create($container);
    $instance->account = $container->get('current_user');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var \Drupal\sdv_mapeditor\Entity\SdvMapEntity $entity */

    $form = parent::buildForm($form, $form_state);

    // Group submit handlers in an actions element with a key of "actions" so
    // that it gets styled correctly, and so that other modules may add actions
    // to the form. This is not required, but is convention.
//    $form['actions'] = [
//      '#type' => 'actions',
//    ];
//

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    $values = $form_state->getValues();
//    $values = Json::encode($values);
//    $entity->set('gis_ia_params', $values);
    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label map.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label map.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.sdv_map.canonical', ['sdv_map' => $entity->id()]);
  }

}
