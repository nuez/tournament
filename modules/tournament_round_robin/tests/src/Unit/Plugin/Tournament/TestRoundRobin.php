<?php


namespace Drupal\Tests\tournament_round_robin\Unit\Plugin\Tournament;
use Drupal\tournament\Entity\Tournament;
use Drupal\tournament_round_robin\Plugin\Tournament\RoundRobin;


/**
 * Class TestRoundRobin
 */
class TestRoundRobin extends RoundRobin {
  public function createMatch(Tournament $tournament, $home_participant_id, $away_participant_id) {
  }
}

