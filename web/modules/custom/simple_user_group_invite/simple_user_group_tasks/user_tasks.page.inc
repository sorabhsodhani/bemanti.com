<?php

/**
 * @file
 * Contains user_tasks.page.inc.
 *
 * Page callback for User Tasks[D[D[D[D[DGroup Tasks entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for User Tasks[D[D[D[D[DGroup Tasks templates.
 *
 * Default template: user_tasks.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_user_tasks(array &$variables) {
  // Fetch UserTasksEntity Entity Object.
  $user_tasks = $variables['elements']['#user_tasks'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
