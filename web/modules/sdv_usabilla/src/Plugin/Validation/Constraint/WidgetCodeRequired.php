<?php

namespace Drupal\sdv_usabilla\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Plugin implementation of the 'widget_code_required'.
 *
 * @Constraint(
 *   id = "widget_code_required",
 *   label = @Translation("Widget code required", context = "Validation"),
 *   type = "entity:usabilla_item"
 * )
 */
class WidgetCodeRequired extends Constraint
{

  /**
   * @var string
   */
  public $widgetCodeRequired = 'The widget code is is required when the type is "widget".';

  /**
   * {@inheritdoc}
   */
  public function coversFields() {
    return ['widget_code', 'type'];
  }


}
