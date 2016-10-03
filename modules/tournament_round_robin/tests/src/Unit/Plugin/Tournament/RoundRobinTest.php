<?php
/**
 * @file
 *  Contains Unit tests for the RoundRobin Plugin Instance.
 */

namespace Drupal\Tests\tournament_round_robin\Unit\Plugin\Tournament;

use Drupal\Tests\UnitTestCase;
use Drupal\tournament\Entity\Participant;
use Drupal\tournament\Entity\Tournament;
use Drupal\tournament\Entity\TournamentInterface;
use Drupal\tournament\Plugin\TournamentManager;
use Drupal\tournament\Plugin\TournamentManagerInterface;
use Drupal\tournament_round_robin\Plugin\Tournament\RoundRobin;
use Prophecy\Prophecy\ObjectProphecy;
use spec\Prophecy\Prophecy\ObjectProphecySpec;

/**
 * @coversDefaultClass \Drupal\tournament_round_robin\Plugin\Tournament\RoundRobin
 *
 * @group Tournament
 */
class RoundRobinTest extends UnitTestCase {


  /**
   * @var TournamentInterface|ObjectProphecy
   */
  protected $tournament;

  /**
   * @var TournamentManager|ObjectProphecy
   */
  protected $tournamentManager;

  /**
   * @var Participant
   */
  protected $participant;

  /**
   * The System Under Test.
   *
   * @var RoundRobin
   */
  protected $sut;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

  }

  /**
   * @covers ::generateMatches
   *
   * @dataProvider getGenerateMatchScenarios
   *
   * Generates matches based on amount of rounds and participants.
   */
  public function testGenerateMatches($rounds, $participants_number, $expected_matches) {

    /** @var \PHPUnit_Framework_MockObject_MockObject $participant */
    $tournamentMock = $this->getMockBuilder(Tournament::class)
      ->setMethods(['getParticipants', 'getConfig'])
      ->disableOriginalConstructor()
      ->getMock();

    /** @var \PHPUnit_Framework_MockObject_MockObject $participant */
    $participantMock = $this->getMockBuilder(Participant::class)
      ->setMethods(['id'])
      ->disableOriginalConstructor()
      ->getMock();

    // Mock the tournament entity and pass on the amount of rounds  to
    // the configuration BaseField.
    $tournamentMock->expects($this->any())
      ->method('getConfig')
      ->willReturn([
        'rounds' => $rounds,
        'interval' => 60 * 60 * 24 * 7,
        'shuffle' => TRUE,
        'start_time' => (int) $_SERVER['REQUEST_TIME'],
      ]);

    // The getParticipants method would normally return an array of Participants
    // for the tournament. In this case We use Mocks of the participants.
    // Since the id of the participant is used for the generated match (through
    // the id() method, we need to stub the id() method and make it return
    // the index of the iteration.
    $participants = [];
    for ($i = 0; $i < $participants_number; $i++) {
      $participantMock->expects($this->at($i))
        ->method('id')
        ->willReturn(1 + $i);
      $participants[] = $participantMock;
    }

    // The getParticipants() method will return the array of Mock Participants
    // previously defined.

    /** @var ObjectProphecySpec $tournamentMananagerProphesy */
    $tournamentMananagerProphesy = $this->prophesize(TournamentManager::class);
    $tournamentMananagerProphesy->getParticipants($tournamentMock)->willReturn($participants);

    $this->tournamentManager = $tournamentMananagerProphesy->reveal();

    $this->sut = new TestRoundRobin([], '', [], $this->tournamentManager);

    $matches = $this->sut->generateMatches($tournamentMock);

    // Assert that the total amount of matches is correct.
    $this->assertEquals($expected_matches, count($matches));


  }

  /**
   * Data provider for ...
   */
  public function getGenerateMatchScenarios() {
    return [
      [
        'rounds' => 1,
        'participants_number' => 4,
        'expected_number_of_matches' => 6,
      ],
      [
        'rounds' => 2,
        'participants_number' => 4,
        'expected_number_of_matches' => 12,
      ],
      [
        'rounds' => 1,
        'participants_number' => 5,
        'expected_number_of_matches' => 10,
      ],
      [
        'rounds' => 2,
        'participants_number' => 5,
        'expected_number_of_matches' => 20,
      ],
    ];

  }

}
