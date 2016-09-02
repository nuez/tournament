<?php

/**
 * @file
 * Contains tournament_team.page.inc.
 *
 * Page callback for Team entities.
 */

use Drupal\Core\Render\Element;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Prepares variables for Team templates.
 *
 * Default template: tournament_team.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_tournament_team(array &$variables) {
  // Fetch Team Entity Object.
  $tournament_team = $variables['elements']['#tournament_team'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
