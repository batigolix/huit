<?php

/**
 * @file
 * Contains sdv_map.page.inc.
 *
 * Page callback for Map entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Map templates.
 *
 * Default template: sdv_map.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_sdv_map(array &$variables) {
  // Fetch SdvMapEntity Entity Object.
  $sdv_map = $variables['elements']['#sdv_map'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
