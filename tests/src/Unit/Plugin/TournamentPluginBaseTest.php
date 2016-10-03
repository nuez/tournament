<?php
/**
 * @file
 * Contains \Drupal\Tests\tournament\Unit\Plugin\TournamentPluginBaseTest.php
 */
namespace Drupal\Tests\tournament\Unit\TournamentPluginBaseTest;

use Drupal\Tests\UnitTestCase;
use Drupal\tournament\Entity\Match;
use Drupal\tournament\Entity\MatchResult;
use Drupal\tournament\Plugin\TournamentInterface;
use Drupal\tournament\Plugin\TournamentPluginBase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @coversDefaultClass \Drupal\tournament\Plugin\TournamentPluginBase
 *
 * @group Tournament
 */
class TournamentPluginBaseTest extends UnitTestCase {

  const SAVED_UPDATED = 2;

  /**
   * @var TournamentPluginBase|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $sut;

  /**
   * The Match
   *
   * @var Match|ObjectProphecy
   */
  protected $match;

  /**
   * The MatchResult
   *
   * @var MatchResult|ObjectProphecy
   */
  protected $matchResult;

  public function setUp() {
    parent::setUp();

    // Prophesize the match instance.
    $this->match = $this->prophesize(Match::class);

    // Prophesize the MatchResult instance.
    $this->matchResult = $this->prophesize(MatchResult::class);

    // Because this class is an abstract class, we need to use a
    // special Mock for testing.
    $this->sut = $this->getMockBuilder(TournamentPluginBase::class)
      ->disableOriginalConstructor()
      ->getMockForAbstractClass();
  }

  /**
   * Test exception when match status is already processed.
   *
   * Should generate an exception as the Match has already been processed.
   *
   * @covers ::processMatchResult
   *
   * @expectedException \Exception
   */
  public function testAlreadyProcessedMatchException() {
    $this->match->getStatus()->willReturn(Match::PROCESSED);
    $this->sut->processMatchResult($this->match->reveal(), []);
  }

  /**
   * Test exception when match status is not yet confirmed.
   *
   * Should generate an exception as the Match has already been processed.
   *
   * @covers ::processMatchResult
   *
   * @expectedException \Exception
   */
  public function testAwaitingConfirmationMatchException() {
    $this->match->getStatus()->willReturn(Match::AWAITING_CONFIRMATION);
    $this->sut->processMatchResult($this->match->reveal(), []);
  }

  /**
   * Test exception when match status is not yet confirmed.
   *
   * Should generate an exception as the Match has already been processed.
   *
   * @covers ::processMatchResult
   */
  public function testMatchProcessingForCorrectMatchStatus() {
    $this->match->getStatus()->willReturn(Match::CONFIRMED);

    // Since the global SAVED_UPDATED constant isn't bootstrapped (common.inc)
    // we need to work with our own constant.
    $this->match->getMatchResults()->willReturn([]);
    $this->match->set("status", MATCH::PROCESSED)->willReturn($this->match->reveal());
    $this->match->save()->willReturn(self::SAVED_UPDATED);

    $this->sut->processMatchResult($this->match->reveal(), []);

    // This shouldn't return anything so no assertion is made.
    // That's why we assert TRUE as TRUE.
    // @todo See if there is a better way to assert void methods.
    $this->assertTrue(TRUE);
  }


  /**
   * Test exception when amount of results doesn't match the amount of
   * MatchResult entities.
   *
   * @covers ::processMatchResult
   *
   * @expectedException \InvalidArgumentException
   */
  public function testMatchResultProcessWithNonMatchingResultAmounts() {
    $this->match->getStatus()->willReturn(Match::CONFIRMED);

    // Return two MatchResult entities when calling the getMatchResults method.
    $this->match->getMatchResults()->willReturn([
      $this->matchResult->reveal(),
      $this->matchResult->reveal(),
    ]);
    $this->match->save()->willReturn(self::SAVED_UPDATED);

    // Pass 3 result integers to the method. As 2 != 3, we should get an
    // Exception.
    $this->sut->processMatchResult($this->match->reveal(), [1,2,3]);

  }
}
