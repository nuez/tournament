<?php
/**
 * @file
 * Contains all Kernel tests for the Tournament Manager.
 */

namespace Drupal\Tests\tournament\Kernel\Entity;

use Drupal\Component\DependencyInjection\Container;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\KernelTests\KernelTestBase;
use Drupal\tournament\Entity\Team;
use Drupal\tournament\Entity\Tournament;
use Drupal\tournament\Exception\TournamentException;
use Drupal\tournament\Plugin\TournamentManager;
use Drupal\tournament\Plugin\TournamentPluginInterface;
use Drupal\user\Entity\User;

/**
 * Kernel Tests for Exceptions for Tournament.
 *
 * @package Drupal\Tests\tournament\Kernel
 *
 * @group Tournament
 *
 */
class TournamentExceptionTest extends KernelTestBase {

  /**
   * @var Tournament
   */
  protected $tournament;

  /**
   * @var TournamentManager
   */
  protected $tournamentManager;

  /**
   * @var TournamentPluginInterface
   */
  protected $tournamentPlugin;

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
    'user',
    'config',
  ];

  public function setUp() {
    parent::setUp();
    $this->installEntitySchema('tournament');
    $this->installEntitySchema('tournament_participant');
    $this->installEntitySchema('tournament_match');
    $this->installEntitySchema('user');
    $this->installSchema('system', ['sequences']);

    // To install default fields on the Tournament Participant
    // entity we need to call the installConfig method.
    $this->installConfig(['tournament']);

    $this->tournamentManager = \Drupal::getContainer()
      ->get('plugin.manager.tournament.manager');
    $tournament_types = $this->tournamentManager->getDefinitions();
    $tournament_type = array_rand($tournament_types);

    // Create a Tournament for testing.
    $tournament = Tournament::create([
      'type' => $tournament_type,
      'created' => REQUEST_TIME,
      'updated' => REQUEST_TIME,
      'user_id' => 1,
      'name' => $this->randomMachineName(),
      'status' => Tournament::STATUS_UNSTARTED,
      'participant_type' => 'user',
    ]);
    $tournament->save();
    $this->tournament = $tournament;

    // Instantiate the TournamentManager.
    $this->tournamentManager = \Drupal::getContainer()
      ->get('plugin.manager.tournament.manager');

    $this->tournamentPlugin = $this->tournamentManager->createInstance($tournament_type);
  }

  /**
   * Don't allow participant types other than the ones defined as bundles.
   *
   * @expectedException \Drupal\Core\Entity\EntityStorageException
   * @expectedExceptionCode 100
   */
  public function testInvalidParticipantType() {

    // Creating a tournament with a random participant type machine name,
    // should throw an exception. The expected Exception is an
    // EntityStorageException since the TournamentException that is thrown
    // initially will be caught by SqlContentEntityStorage::save method.
    $tournament = Tournament::create([
      'type' => $this->randomMachineName(),
      'created' => REQUEST_TIME,
      'updated' => REQUEST_TIME,
      'user_id' => 1,
      'name' => $this->randomMachineName(),
      'status' => Tournament::STATUS_UNSTARTED,
      'participant_type' => $this->randomMachineName(),
      $bundleService = \Drupal::getContainer()->get('')
    ]);
    $tournament->save();
  }


  /**
   * Test Exception when changing participant type after participants have been added.
   *
   * @expectedException \Drupal\Core\Entity\EntityStorageException
   * @expectedExceptionCode 101
   */
  public function testExceptionChangeParticipantTypeAfterAddingParticipants() {
    $users = $this->generateTestUsers();
    $this->tournamentPlugin->addParticipants($this->tournament, $users);
    $this->tournament->set('participant_type', 'team');

    // The presave method should throw an exception disallowing the change
    // of participant type after participants have been added.
    $this->tournament->save();
  }

  /**
   * Don't allow adding more participants after Tournament has started.
   *
   * @expectedException \Drupal\Core\Entity\EntityStorageException
   * @expectedExceptionCode 102
   */
  public function testDisallowAddParticipantsAfterStarting() {

    /** @var TournamentManager $tournament_manager */
    $this->tournamentManager = \Drupal::service('plugin.manager.tournament.manager');
    $tournament_types = $this->tournamentManager->getDefinitions();
    $tournament_type = array_rand($tournament_types);

    $this->tournamentPlugin = $this->tournamentManager->createInstance($tournament_type);

    // Add some users, then start the tournament, and add some more
    // participants. This shouldn't be allowed.
    $users = $this->generateTestUsers();
    $this->tournamentPlugin->addParticipants($this->tournament, $users);
    $this->tournamentPlugin->startTournament($this->tournament);

    // Create more users and try to add them to the tournament.
    $moreUsers = $this->generateTestUsers();
    $this->tournamentPlugin->addParticipants($this->tournament, $moreUsers);
  }

  /**
   * Don't allow adding the same participant twice.
   *
   * @expectedException \Drupal\Core\Entity\EntityStorageException
   * @exceptionExceptionCode 103
   *
   */
  public function testDisallowDuplicateParticipants() {
    $users = $this->generateTestUsers();
    $this->tournamentPlugin->addParticipants($this->tournament, $users);
    $this->tournamentPlugin->addParticipants($this->tournament, $users);
  }


  /**
   * Generate a bunch of users.
   *
   * @return User[]
   */
  private function generateTestUsers() {
    $users = [];
    for ($i = 0; $i < rand(2, 10); $i++) {
      $user = User::create([
        'name' => $this->randomGenerator->name(),
        'mail' => $this->randomMachineName() . '@email.com',
      ]);
      $user->save();
      $users[] = $user;
    }
    return $users;
  }

  /**
   * Generate a bunch of teams.
   */
  private function generateTestTeams() {
    $teams = [];
    for ($i = 0; $i < rand(2, 10); $i++) {
      $team = Team::create([
        'label' => $this->randomGenerator->name(),
      ]);
      $team->save();
      $teams[] = $team;
    }
    return $teams;
  }

}