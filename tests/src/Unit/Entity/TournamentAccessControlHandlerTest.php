<?php
/**
 * @file
 *
 * Contains \Drupal\Tests\tournament\Entity\TournamentAccessControlHanderTest
 */

use Drupal\Tests\UnitTestCase;
use Drupal\tour\Entity\Tour;
use Drupal\tournament\Entity\Participant;
use Drupal\tournament\Entity\Tournament;
use Drupal\tournament\Entity\TournamentAccessControlHandler;
use Drupal\tournament\Plugin\TournamentManager;
use Prophecy\Prophecy\ProphecyInterface;

/**
 * Class TournamentAccessControlHandlerTest
 *
 * @coversDefaultClass \Drupal\tournament\Entity\TournamentAccessControlHandler
 *
 * @group Tournament
 */
class TournamentAccessControlHandlerTest extends UnitTestCase {
  /**
   * @var TournamentAccessControlHandler
   */
  protected $sut;

  /**
   * @var Tournament|ProphecyInterface
   */
  protected $tournament;

  /**
   * @var TournamentManager|ProphecyInterface
   */
  protected $tournamentManager;

  public function setUp() {
    parent::setUp();
    $this->sut = new TournamentAccessControlHandler('tournament');
    $this->tournament = $this->prophesize(Tournament::class);
    $this->tournamentManager = $this->prophesize(TournamentManager::class);

  }


  /**
   * @covers \Drupal\tournament\Entity\TournamentAccessControlHandler::fieldAccess
   */
  public function testFieldAccess() {
    $this->tournamentManager->getParticipants()->willReturn([
      $this->prophesize(Participant::class)->reveal(),
      $this->prophesize(Participant::class)->reveal(),
    ]);


  }
}