<?php

/**
 * @file
 * Contains \Drupal\tournament\Plugin\TournamentInterface.
 */

namespace Drupal\tournament\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\tournament\Entity\Tournament;
use Drupal\tournament\Entity\TournamentInterface;

/**
 * Defines an interface for Tournament plugins.
 */
interface TournamentPluginInterface extends PluginInspectionInterface {

  /**
   * @param TournamentInterface $tournament
   *
   * @return Match[].
   */
  public function generateMatches(TournamentInterface $tournament);

}