<?php
/**
 * @file
 * Contains all Kernel tests for the Tournament Manager.
 */

namespace Drupal\Tests\tournament\Kernel\Plugin;

use Drupal\KernelTests\KernelTestBase;
use Drupal\tournament\Entity\Match;
use Drupal\tournament\Entity\Participant;
use Drupal\tournament\Entity\Tournament;
use Drupal\tournament\Plugin\TournamentManager;

/**
 * Class TournamentManagerTest
 *
 * @package Drupal\Tests\tournament\Kernel\Plugin
 * @coversDefaultClass Drupal\tournament\Plugin\TournamentManager
 * @group Tournament
 *
 */
class TournamentManagerTest extends KernelTestBase {

  /**
   * @var TournamentManager
   */
  protected $sut;

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

  public function setUp() {
    parent::setUp();
    $this->installEntitySchema('tournament');
    $this->installEntitySchema('tournament_participant');
    $this->installEntitySchema('tournament_match');
    $this->sut = \Drupal::getContainer()
      ->get('plugin.manager.tournament.manager');
  }

  /**
   * @covers ::getMatches
   */
  public function testGetMatches() {
    // Create a tournament and matches.
    // The getMatches
    $tournamentA = Tournament::create([
      'type' => $this->randomMachineName(),
      'created' => REQUEST_TIME,
      'updated' => REQUEST_TIME,
      'user_id' => 1,
      'name' => $this->randomMachineName(),
      'status_publisehd' => TRUE,
      'status_started' => TRUE,
    ]);
    $tournamentA->save();
    $tournamentB = Tournament::create([
      'type' => $this->randomMachineName(),
      'created' => REQUEST_TIME,
      'updated' => REQUEST_TIME,
      'user_id' => 1,
      'name' => $this->randomMachineName(),
      'status_publisehd' => TRUE,
      'status_started' => TRUE,
    ]);
    $tournamentB->save();
    $matchesA = [];
    // Create some matches that belong to Tournament A.
    for($i = 0; $i <= rand(0,10); $i++){
      $match = Match::create([
        'tournament_reference' => $tournamentA->id(),
        'status' => Match::PROCESSED
      ]);
      $match->save();
      $matchesA[] = \Drupal::entityTypeManager()->getStorage('tournament_match')->load($match->id());
    }

    $matchesB = [];
    // Create some other matches that belong to Tournament B.
    for($i = 0; $i <= rand(0,10); $i++){
      $match = Match::create([
        'tournament_reference' => $tournamentB->id(),
        'status' => Match::PROCESSED
      ]);
      $match->save();
      $matchesB[] =  \Drupal::entityTypeManager()->getStorage('tournament_match')->load($match->id());
    }

    // The method should return an array of Match objects.
    // The first time it will use the entityTypeManager,
    // after which it should asign the matches to the tournament->matches
    // property. It should load correctly both times.
    $this->assertEquals(count($matchesA),count($this->sut->getMatches($tournamentA)));
    foreach($matchesA as $match){
      $this->assertInstanceOf(Match::class, $match);
    }
    $this->assertEquals(count($matchesA),count($this->sut->getMatches($tournamentA)));
    foreach($matchesA as $match){
      $this->assertInstanceOf(Match::class, $match);
    }

    // Alo check the second batch, to make sure the right ones are loaded.
    $this->assertEquals(count($matchesB),count($this->sut->getMatches($tournamentB)));
    foreach($matchesB as $match){
      $this->assertInstanceOf(Match::class, $match);
    }
    $this->assertEquals(count($matchesB),count($this->sut->getMatches($tournamentB)));
    foreach($matchesB as $match){
      $this->assertInstanceOf(Match::class, $match);
    }
  }

  /**
   * @covers ::getParticipants
   *
   * Test if the method returns an array of Participant entities.
   */
  public function testGetParticipants(){
    // Create a tournament and matches.
    // The getMatches
    $tournamentA = Tournament::create([
      'type' => $this->randomMachineName(),
      'created' => REQUEST_TIME,
      'updated' => REQUEST_TIME,
      'user_id' => 1,
      'name' => $this->randomMachineName(),
      'status_publisehd' => TRUE,
      'status_started' => TRUE,
    ]);
    $tournamentA->save();
    $tournamentB = Tournament::create([
      'type' => $this->randomMachineName(),
      'created' => REQUEST_TIME,
      'updated' => REQUEST_TIME,
      'user_id' => 1,
      'name' => $this->randomMachineName(),
      'status_publisehd' => TRUE,
      'status_started' => TRUE,
    ]);
    $tournamentB->save();
    $participantsA = [];

    // Create some other matches that belong to Tournament A.
    for($i = 0; $i <= rand(0,10); $i++){
      $participant = Participant::create([
        'type' => $this->randomMachineName(),
        'name' => $this->randomMachineName(),
        'tournament_reference' => $tournamentA->id(),
      ]);
      $participant->save();
      $participantsA[] = $participant;
    }

    // Create some other matches that belong to Tournament B.
    for($i = 0; $i <= rand(0,10); $i++){
      $participant = Participant::create([
        'type' => $this->randomMachineName(),
        'name' => $this->randomMachineName(),
        'tournament_reference' => $tournamentB->id(),
      ]);
      $participant->save();
      $participantsB[] = $participant;
    }

    // Tournament A - First time
    $this->assertEquals(count($participantsA),count($this->sut->getParticipants($tournamentA)));
    foreach($participantsA as $participant){
      $this->assertInstanceOf(Participant::class, $participant);
    }

    // Tournament A - Second time
    $this->assertEquals(count($participantsA),count($this->sut->getParticipants($tournamentA)));
    foreach($participantsA as $participant){
      $this->assertInstanceOf(Participant::class, $participant);
    }

    // Tournament B - First time
    $this->assertEquals(count($participantsB),count($this->sut->getParticipants($tournamentB)));
    foreach($participantsB as $participant){
      $this->assertInstanceOf(Participant::class, $participant);
    }

    // Tournament B - Second time
    $this->assertEquals(count($participantsB),count($this->sut->getParticipants($tournamentB)));
    foreach($participantsB as $participant){
      $this->assertInstanceOf(Participant::class, $participant);
    }
  }
}