<?php

namespace Drupal\sdv_usabilla\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Validates the UniqueInteger constraint.
 */
class WidgetCodeRequiredValidator extends ConstraintValidator implements ContainerInjectionInterface {

  /**
   * Validator 2.5 and upwards compatible execution context.
   *
   * @var \Symfony\Component\Validator\Context\ExecutionContextInterface
   */
  protected $context;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('entity.manager')
      ->getStorage('usabilla_item'));
  }

  /**
   * {@inheritdoc}
   */
  public function validate($entity, Constraint $constraint) {
    if ($entity->hasField('widget_code') && $entity->hasField('type')) {
      $type = $entity->get('type')->getValue();
      $type = (count($type) == 1 ? $type[0]['value'] : FALSE);
      $widget_code = $entity->get('widget_code')->getValue();
      $widget_code = (count($widget_code) == 1 ? $widget_code[0]['value'] : FALSE);
      if (isset($type) && $type === 'widget' && empty($widget_code)) {
        $this->context->buildViolation($constraint->widgetCodeRequired)
          ->atPath('widget_code')
          ->addViolation();
      }
    }
  }

}
