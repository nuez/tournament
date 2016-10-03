<?php
/**
 * @file
 *
 * Contains \Drupal\Tests\tournament\Controller\ParticipantControllerTests.php
 */


namespace Drupal\Tests\tournament\Unit\Controller;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Entity\EntityStorageBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\StringTranslation\TranslationManager;
use Drupal\Tests\UnitTestCase;
use Drupal\tournament\Controller\ParticipantController;

/**
 * Class ParticipantControllerTest
 *
 * @coversDefaultClass \Drupal\tournament\Controller\ParticipantController
 *
 * @group Tournament
 */
class ParticipantControllerTest extends UnitTestCase {

  /**
   * @var TranslationManager $translationManager ;
   */
  protected $translationManager;

  /**
   * @var EntityStorageInterface $storage
   */
  protected $storage;

  /**
   * @var \Drupal\tournament\Controller\ParticipantController $sut;
   */
  protected $sut;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->storage = $this->prophesize(EntityStorageBase::class)->reveal();

    $this->sut = new ParticipantController($this->storage);

    // Mock the ConfigFactory service.
    $this->translationManager = $this->getMockBuilder('\Drupal\Core\StringTranslation\TranslationManager')
      ->disableOriginalConstructor()
      ->getMock();


    $this->container = new ContainerBuilder();
    $this->container->set('string_translation', $this->translationManager);
    \Drupal::setContainer($this->container);

  }

  /**
   * @covers \Drupal\tournament\Entity\TournamentAccessControlHandler::checkAccess
   */
  public function testView() {
    $view = $this->sut->view();

    /** @var TranslatableMarkup $markup */
    $markup = $view['#markup'];

    // Make this test fail so we know we have some work to do here.
    $this->assertSame($markup->getUntranslatedString(), FALSE);
  }
}