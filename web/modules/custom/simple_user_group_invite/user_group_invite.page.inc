<?php

/**
 * @file
 * Contains user_group_invite.page.inc.
 *
 * Page callback for User Group Invites entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for User Group Invites templates.
 *
 * Default template: user_group_invite.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_user_group_invite(array &$variables) {
  // Fetch UserGroupInviteEntity Entity Object.
  $user_group_invite = $variables['elements']['#user_group_invite'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
