<?php

/**
 * @file
 * Contains bemanti_user_bet_entity.page.inc.
 *
 * Page callback for Bemanti user bet entity entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Bemanti user bet entity templates.
 *
 * Default template: bemanti_user_bet_entity.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_bemanti_user_bet_entity(array &$variables) {
  // Fetch BemantiUserBetEntity Entity Object.
  $bemanti_user_bet_entity = $variables['elements']['#bemanti_user_bet_entity'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
