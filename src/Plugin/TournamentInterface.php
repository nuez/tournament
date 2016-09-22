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

  /**
   * Generates matches for this tournament.
   */
  public function generateMatches();

  /**
   * @return mixed array of Match entities.
   */
  public function getMatches();
}
