<?php
/**
 * @file
 *
 * Contains \Drupal\Tests\tournament\Entity\TournamentAccessControlHanderTest
 */

use Drupal\Tests\UnitTestCase;

/**
 * Class TournamentAccessControlHandlerTest
 *
 * @coversDefaultClass \Drupal\tournament\Entity\TournamentAccessControlHandler
 *
 * @group Tournament
 */
class TournamentAccessControlHandlerTest extends UnitTestCase{
  public function setUp() {
    parent::setUp();
  }

  /**
   * @covers \Drupal\tournament\Entity\TournamentAccessControlHandler::checkAccess
   */
  public function testAccess(){
    $this->assertTrue(TRUE);
  }
}