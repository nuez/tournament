<?php

/**
 * @file
 * Contains \Drupal\tournament\Plugin\TournamentInterface.
 */

namespace Drupal\tournament\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\tournament\Entity\Match;
use Drupal\tournament\Entity\MatchResult;
use Drupal\tournament\Entity\Participant;
use Drupal\tournament\Entity\Team;
use Drupal\tournament\Entity\Tournament;
use Drupal\tournament\Entity\TournamentInterface;
use Drupal\user\Entity\User;

/**
 * Defines an interface for Tournament plugins.
 */
interface TournamentPluginInterface extends PluginInspectionInterface {

  /**
   * @param \Drupal\tournament\Entity\Tournament $tournament
   * @param $participants Team[]|User[]
   *
   * @return \Drupal\tournament\Entity\Participant[]
   */
  public function addParticipants(Tournament $tournament, $participants);

  /**
   * @param \Drupal\tournament\Entity\Tournament $tournament
   * @return mixed
   */
  public function startTournament(Tournament $tournament);

  /**
   * @param TournamentInterface $tournament
   *
   * @return Match[].
   */
  public function generateMatches(TournamentInterface $tournament);

  /**
   * Process the results and update the tournament.
   *
   * @param \Drupal\tournament\Entity\Match $match
   *   The Match Entity to update.
   *
   * @param int[] $results
   */
  public function processMatchResult(Match $match, $results);


}
