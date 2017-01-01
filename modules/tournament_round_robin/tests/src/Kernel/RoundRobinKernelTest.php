<?php
/**
 * @file
 * Contains all Tournament Kernel tests related to calculating results.
 */

namespace Drupal\Tests\tournament_round_robin\Kernel;

use Drupal\Core\Language\Language;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\tournament\Unit\TournamentPluginBaseTest\TournamentPluginBaseTest;
use Drupal\tournament\Entity\Match;
use Drupal\tournament\Entity\MatchResult;
use Drupal\tournament\Entity\Participant;
use Drupal\tournament\Entity\Tournament;
use Drupal\tournament\Plugin\TournamentInterface;
use Drupal\tournament\Plugin\TournamentManager;
use Drupal\tournament\Plugin\TournamentManagerInterface;
use Drupal\tournament\Plugin\TournamentPluginInterface;

/**
 * Class RoundRobinKernelTest
 *
 * @coversDefaultClass Drupal\tournament_round_robin\Plugin\Tournament\RoundRobin
 *
 * @group Tournament
 *
 */
class RoundRobinKernelTest extends KernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'system',
    'tournament',
    'field',
    'tournament_round_robin',
    'plugin',
    'field',
    'options',
    'text',
    'user'
  ];

  /**
   * @var TournamentManagerInterface $tournamentManager
   */
  protected $tournamentManager;

  /**
   * A Participant entity.
   *
   * @var Tournament
   */
  protected $tournament;

  /**
   * A Tournament Plugin
   *
   * @var TournamentPluginInterface $tournamentPlugin
   */
  protected $tournamentPlugin;

  /**
   * an array of participants belonging to the tournament.
   *
   * @var array
   */
  protected $participants;

  /**
   * @var Match[]
   */
  protected $matches;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installEntitySchema('tournament');
    $this->installEntitySchema('tournament_participant');
    $this->installEntitySchema('tournament_match');
    $this->installEntitySchema('tournament_match_result');

    /** @var TournamentManager $tournament_manager */
    $this->tournamentManager = \Drupal::service('plugin.manager.tournament.manager');
    $tournament_types = $this->tournamentManager->getDefinitions();
    $tournament_type = array_rand($tournament_types);

    $tournament = Tournament::create([
      'type' => 'tournament_' . $tournament_type,
      'participant_type' => 'user',
      'name' => $this->randomString(),
      'uid' => 1,
      'status' => Tournament::STATUS_UNSTARTED,
      'langcode' => Language::LANGCODE_NOT_SPECIFIED,
      'created' => REQUEST_TIME,
      'updated' => REQUEST_TIME,
      'config' => [
        'rounds' => 2,
        'interval' => 60 * 60 * 24 * 7,
        'shuffle' => TRUE,
        'start_time' => REQUEST_TIME,
        'points_win' => 3,
        'points_draw' => 1,
        'points_loss' => -1,
      ],
    ]);
    $tournament->save();

    $total_participants = rand(5, 10);

    for ($i = 0; $i < $total_participants; $i++) {
      $participant = Participant::create([
        'type' => $tournament->getType(),
        'tournament_reference' => $tournament->id(),
        'points' => 0,
        'win' => 0,
        'draw' => 0,
        'loss' => 0,
        'score_for' => 0,
        'score_against' => 0,
      ])->save();

      $tournament->participants[] = Participant::load($participant);

    }

    $this->tournamentPlugin = $this->tournamentManager->createInstance($tournament_type);

    $this->tournament = $tournament;
  }

  /**
   * Test the generation of matches.
   */
  public function testGenerateMatches() {

    $this->matches = $this->tournamentPlugin->generateMatches($this->tournament);
    // We make no assumptions of how many matches should be generated,
    // all we know that it should generate more than 0.
    $this->assertTrue(count($this->matches) > 0);

    // All the matches should be Match entities.
    foreach ($this->matches as $match) {

      // Should return a Match entity and the match entities in itself
      // should have MatchResult entities.
      $this->assertInstanceOf(Match::class, $match);
      $matchResults = $match->get('match_results')->referencedEntities();

      // All Match entities should have 2 match result entities with
      // associated participant entities.
      $this->assertTrue(2 == count($matchResults));
      $this->assertInstanceOf(MatchResult::class, $matchResults[0]);
      $this->assertInstanceOf(MatchResult::class, $matchResults[1]);

      // Check if the MatchResult entities have an entity reference.
      $participant_home = $matchResults[0]->get('participant')
        ->referencedEntities()[0];
      $participant_away = $matchResults[1]->get('participant')
        ->referencedEntities()[0];
      $this->assertInstanceOf(Participant::class, $participant_home);
      $this->assertInstanceOf(Participant::class, $participant_away);
      $this->assertNotEquals($participant_home->id(), $participant_away->id());
    }

  }

  /**
   * Test processing of Match Results for the winner.
   *
   * Exceptions are tested in the corresponding unit test.
   *
   * @see TournamentPluginBaseTest
   */
  public function testProcessMatchResultWin() {
    $this->matches = $this->tournamentPlugin->generateMatches($this->tournament);

    // The configuration of the tournament is set in ::setUp().
    // 3 Points for the winner, -1 for the looser, and 1 for a draw.

    /** @var Match $match */
    $match = $this->matches[0];

    // Set the match status to confirmed, so it doesn't return an exception.
    $match->set('status', Match::CONFIRMED)->save();

    // Foo is the loser, Bar is the winner. Their match result is 3 against 6.

    $expected_result_winner = [
      'win' => 1,
      'draw' => 0,
      'loss' => 0,
      'points' => 3,
      'score_for' => 6,
      'score_against' => 3,
    ];

    $this->tournamentPlugin->processMatchResult($match, [3, 6]);

    $participants = $match->getParticipants();

    $result_winner = [
      'win' => $participants[1]->get('win')->getString(),
      'draw' => $participants[1]->get('draw')->getString(),
      'loss' => $participants[1]->get('loss')->getString(),
      'points' => $participants[1]->get('points')->getString(),
      'score_for' => $participants[1]->get('score_for')->getString(),
      'score_against' => $participants[1]->get('score_against')->getString(),
    ];

    // Assert the scores of the loser.
    $this->assertEquals($expected_result_winner, $result_winner);

  }


  /**
   * Test processing of Match Results for the winner.
   *
   * Exceptions are tested in the corresponding unit test.
   *
   * @see TournamentPluginBaseTest
   */
  public function testProcessMatchResultLoss() {
    $this->matches = $this->tournamentPlugin->generateMatches($this->tournament);

    // The configuration of the tournament is set in ::setUp().
    // 3 Points for the winner, -1 for the looser, and 1 for a draw.

    /** @var Match $match */
    $match = $this->matches[0];

    // Set the match status to confirmed, so it doesn't return an exception.
    $match->set('status', Match::CONFIRMED)->save();

    // Foo is the loser, Bar is the winner. Their match result is 3 against 6.

    $expected_result_loser = [
      'win' => 0,
      'draw' => 0,
      'loss' => 1,
      'points' => -1,
      'score_for' => 3,
      'score_against' => 6,
    ];

    $this->tournamentPlugin->processMatchResult($match, [3, 6]);

    $participants = $match->getParticipants();

    $result_loser = [
      'win' => $participants[0]->get('win')->getString(),
      'draw' => $participants[0]->get('draw')->getString(),
      'loss' => $participants[0]->get('loss')->getString(),
      'points' => $participants[0]->get('points')->getString(),
      'score_for' => $participants[0]->get('score_for')->getString(),
      'score_against' => $participants[0]->get('score_against')->getString(),
    ];

    // Assert the scores of the loser.
    $this->assertEquals($expected_result_loser, $result_loser);

  }


  /**
   * Test processing of Match Results for a draw game.
   *
   * Exceptions are tested in the corresponding unit test.
   *
   * @see TournamentPluginBaseTest
   */
  public function testProcessMatchResultDraw() {
    $this->matches = $this->tournamentPlugin->generateMatches($this->tournament);

    // The configuration of the tournament is set in ::setUp().

    /** @var Match $match */
    $match = $this->matches[0];

    // Set the match status to confirmed, so it doesn't return an exception.
    $match->set('status', Match::CONFIRMED)->save();

    // The expected outcome of a draw game.
    $expected_result_draw = [
      'win' => 0,
      'draw' => 1,
      'loss' => 0,
      'points' => 1,
      'score_for' => 3,
      'score_against' => 3,
    ];

    $this->tournamentPlugin->processMatchResult($match, [3, 3]);

    $participants = $match->getParticipants();

    $result_draw = [
      'win' => $participants[0]->get('win')->getString(),
      'draw' => $participants[0]->get('draw')->getString(),
      'loss' => $participants[0]->get('loss')->getString(),
      'points' => $participants[0]->get('points')->getString(),
      'score_for' => $participants[0]->get('score_for')->getString(),
      'score_against' => $participants[0]->get('score_against')->getString(),
    ];

    // Assert the scores of the loser.
    $this->assertEquals($expected_result_draw, $result_draw);

  }

}