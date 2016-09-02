<?php

/**
 * @file
 * Contains participant.page.inc.
 *
 * Page callback for Participant entities.
 */

use Drupal\Core\Render\Element;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Prepares variables for Participant templates.
 *
 * Default template: participant.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_participant(array &$variables) {
  // Fetch Participant Entity Object.
  $participant = $variables['elements']['#participant'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
