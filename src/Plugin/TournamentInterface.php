<?php

/**
 * @file
 * Contains \Drupal\tournament\Plugin\TournamentInterface.
 */

namespace Drupal\tournament\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Defines an interface for Tournament plugins.
 */
interface TournamentInterface extends PluginInspectionInterface {
  public function generateMatches();
}
