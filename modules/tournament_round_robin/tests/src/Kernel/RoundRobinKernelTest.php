<?php
/**
 * @file
 * Contains all Tournament Kernel tests related to calculating results.
 */

namespace Drupal\Tests\tournament_round_robin\Kernel;

use Drupal\Core\Language\Language;
use Drupal\KernelTests\KernelTestBase;
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
      $participant_home = $matchResults[0]->get('participant')->referencedEntities()[0];
      $participant_away = $matchResults[1]->get('participant')->referencedEntities()[0];
      $this->assertInstanceOf(Participant::class, $participant_home);
      $this->assertInstanceOf(Participant::class, $participant_away);
      $this->assertNotEquals($participant_home->id(), $participant_away->id());
    }

  }


  /**
   * Test processing the scores.
   */
  public function testScoreProcessing(){
    $this->matches = $this->tournamentPlugin->generateMatches($this->tournament);

    /** @var Match $match */
    $match = $this->matches[0];

    $match->set('status', 8);

    $match->processResult([10,20]);
  }


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
      'status_published' => TRUE,
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
        'points_loss' => 0,
      ],
    ]);
    $tournament->save();

    $total_participants = 4;

    for ($i = 0; $i < $total_participants; $i++) {
      $participant = Participant::create([
        'type' => $tournament->getType(),
        'tournament_reference' => $tournament->id(),
        'points' => 0,
        'win' => rand(0, 10),
        'draw' => rand(0, 10),
        'loss' => rand(0, 10),
        'score_for' => rand(0, 10),
        'score_against' => rand(0, 10),
      ])->save();

      $tournament->participants[] = Participant::load($participant);

    }

    $this->tournamentPlugin = $this->tournamentManager->createInstance($tournament_type);

    $this->tournament = $tournament;
  }


}