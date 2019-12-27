<?php

/**
 * @file
 * Contains usabilla_item.page.inc.
 *
 * Page callback for Usabilla item entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Usabilla item templates.
 *
 * Default template: usabilla_item.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_usabilla_item(array &$variables) {
  // Fetch UsabillaItemEntity Entity Object.
  $usabilla_item = $variables['elements']['#usabilla_item'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
