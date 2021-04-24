<?php

/**
 * @file
 * Contains user_group.page.inc.
 *
 * Page callback for User Groups entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for User Groups templates.
 *
 * Default template: user_group.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_user_group(array &$variables) {
  // Fetch UserGroupEntity Entity Object.
  $user_group = $variables['elements']['#user_group'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
