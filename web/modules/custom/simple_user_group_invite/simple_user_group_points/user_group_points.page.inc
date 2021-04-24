<?php

/**
 * @file
 * Contains user_group_points.page.inc.
 *
 * Page callback for User Group points entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for User Group points templates.
 *
 * Default template: user_group_points.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_user_group_points(array &$variables) {
  // Fetch UserGroupPointsEntity Entity Object.
  $user_group_points = $variables['elements']['#user_group_points'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
