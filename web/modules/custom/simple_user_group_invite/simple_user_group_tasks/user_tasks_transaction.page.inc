<?php

/**
 * @file
 * Contains user_tasks_transaction.page.inc.
 *
 * Page callback for User Tasks Transaction entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for User Tasks Transaction templates.
 *
 * Default template: user_tasks_transaction.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_user_tasks_transaction(array &$variables) {
  // Fetch UserTasksTransactionEntity Entity Object.
  $user_tasks_transaction = $variables['elements']['#user_tasks_transaction'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
