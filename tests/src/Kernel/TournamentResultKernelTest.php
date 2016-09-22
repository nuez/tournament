<?php
/**
 * @file
 * Contains all Tournament Kernel tests related to calculating results.
 */

namespace Drupal\Tests\tournament\Kernel;

use Drupal\Core\Language\Language;
use Drupal\KernelTests\KernelTestBase;
use Drupal\tournament\Entity\Participant;
use Drupal\tournament\Entity\Tournament;
use Drupal\tournament\Plugin\TournamentInterface;
use Drupal\tournament\Plugin\TournamentManager;

/**
 * Class TournamentResultKernelTest
 * @group Tournament
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
    'tournament_round_robin',
    'plugin',
    'field',
    'options',
    'text',
    'user'
  ];

  /**
   * A Participant entity.
   *
   * @var Tournament
   */
  protected $tournament;

  /**
   * an array of participants belonging to the tournament.
   *
   * @var array
   */
  protected $participants;

  /**
   * Test arbitrary code.
   */
  public function testTest() {
    $this->assertEquals(TRUE, FALSE);
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installEntitySchema('tournament');
    $this->installEntitySchema('tournament_participant');


    $this->tournament = Tournament::create([
      'type' => 'tournament_round_robin',
      'participant_type' => array_rand(['team', 'user']),
      'name' => $this->randomString(),
      'uid' => 1,
      'status_published' => TRUE,
      'langcode' => Language::LANGCODE_NOT_SPECIFIED,
      'created' => REQUEST_TIME,
      'updated' => REQUEST_TIME,
    ]);
    $this->tournament->save();

    $total_participants = rand(2, 20);

    for ($i = 0; $i < $total_participants; $i++) {
      $participant = Participant::create([
        'type' => $this->tournament->getParticipantType(),
        'tournament_reference' => $this->tournament->id(),

      ]);
      $this->participants[] = $participant;
    }

    /** @var TournamentManager $tournament_manager */
    $tournament_manager = \Drupal::service('plugin.manager.tournament.manager');
    $tournament_types = $tournament_manager->getDefinitions();
    $tournament_type = array_rand($tournament_types);

    /** @var TournamentInterface $tournament_plugin */
    $tournament_plugin = $tournament_manager->createInstance($tournament_type);

    $this->matches = $tournament_plugin->generateMatches();
  }
}