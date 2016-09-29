<?php

/**
 * @file
 * Containes \Drupal\tests\tournament_round_robin\Unit\plugin\Tournament\TestRoundRobin
 */

namespace Drupal\Tests\tournament_round_robin\Unit\Plugin\Tournament;

use Drupal\tournament\Entity\Tournament;
use Drupal\tournament_round_robin\Plugin\Tournament\RoundRobin;

class TestRoundRobin extends RoundRobin {

  // In the createMatch method of the parent we have to call the static
  // Container Wrapper because the container isn't available through
  // the dependency injection. This makes this method hard to test, so
  // we let this method return nothing by extending the original one,
  // as per https://www.drupal.org/docs/8/phpunit/unit-testing-more-complicated-drupal-classes.
  public function createMatch(Tournament $tournament, $home_participant_id, $away_participant_id) {
    // Return Nothing
  }
}