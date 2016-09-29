<?php
/**
 * @file
 * Contains all Tournament Kernel tests related to calculating results.
 */

namespace Drupal\Tests\tournament\Kernel;

use Drupal\Core\Language\Language;
use Drupal\KernelTests\KernelTestBase;
use Drupal\tournament\Entity\Match;
use Drupal\tournament\Entity\Participant;
use Drupal\tournament\Entity\Tournament;
use Drupal\tournament\Plugin\TournamentInterface;
use Drupal\tournament\Plugin\TournamentManager;
use Drupal\tournament\Plugin\TournamentManagerInterface;
use Drupal\tournament\Plugin\TournamentPluginInterface;

/**
 * Class TournamentResultKernelTest
 * @group Tournament
 *
 * This test currently has RoundRobin specific functionality and should
 * be moved to the corresponding module or adapted so it doesn't make
 * assumptions of the Tournament configuration.
 *
 * @todo Fix Round Robin assumtions.
 *
 */
class TournamentResultKernelTest extends KernelTestBase {

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
   * Test arbitrary code.
   */
  public function testGenerateMatches() {

    $this->matches = $this->tournamentPlugin->generateMatches($this->tournament);

    // We make no assumptions of how many matches should be generated,
    // all we know that it should generate more than 0.
    $this->assertTrue(count($this->matches) > 0);

    // All the matches should be Match entities.
    foreach($this->matches as $match){
      $this->assertInstanceOf(Match::class, $match);
    }
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
      'type' => 'tournament_'.$tournament_type,
      'participant_type' => 'user',
      'name' => $this->randomString(),
      'uid' => 1,
      'status_published' => TRUE,
      'langcode' => Language::LANGCODE_NOT_SPECIFIED,
      'created' => REQUEST_TIME,
      'updated' => REQUEST_TIME,
      'config' => [
        'rounds' => 2,
        'interval' => 60*60*24*7,
        'shuffle' => TRUE,
        'start_time' => REQUEST_TIME,
      ],
    ]);
    $tournament->save();

    $total_participants = 4;

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


}