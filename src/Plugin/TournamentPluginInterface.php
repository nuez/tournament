<?php

/**
 * @file
 * Contains \Drupal\tournament\Plugin\TournamentInterface.
 */

namespace Drupal\tournament\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\tournament\Entity\MatchResult;
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

  /**
   * Updating the League participants
   *
   * @param \Drupal\tournament\Entity\MatchResult $matchResult
   * @return mixed
   */
  //public function processMatchResult(MatchResult $matchResult);



}
