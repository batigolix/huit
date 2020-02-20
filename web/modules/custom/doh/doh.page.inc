<?php

/**
 * @file
 * Contains doh.page.inc.
 *
 * Page callback for Doh entity entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Doh entity templates.
 *
 * Default template: doh.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_doh(array &$variables) {
  // Fetch DohEntity Entity Object.
  $doh = $variables['elements']['#doh'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
