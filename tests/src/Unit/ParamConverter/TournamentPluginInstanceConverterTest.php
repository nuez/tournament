<?php
/**
 * @file
 * Contains \Drupal\Tests\tournament\Unit\ParamConverter\TournamentPluginInstanceConverterTest
 */
namespace Drupal\Tests\tournament\Unit\ParamConverter;

use Drupal\Component\Plugin\Exception\ExceptionInterface;
use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\plugin\PluginType\PluginTypeInterface;
use Drupal\plugin\PluginType\PluginTypeManagerInterface;
use Drupal\Tests\UnitTestCase;
use Drupal\tournament\ParamConverter\TournamentPluginInstanceConverter;
use Drupal\tournament\Plugin\TournamentInterface;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Routing\Route;

/**
 * @coversDefaultClass \Drupal\tournament\ParamConverter\TournamentPluginInstanceConverter
 *
 * @group Tournament
 */
class TournamentPluginInstanceConverterTest extends UnitTestCase {

  /**
   * PluginTypeManager.
   *
   * @var PluginTypeManagerInterface|ObjectProphecy
   */
  protected $pluginTypeManager;

  /**
   * PluginManager.
   *
   * @var PluginManagerInterface|ObjectProphecy
   */
  protected $pluginManager;

  /**
   * PluginType.
   *
   * @var PluginTypeInterface|ObjectProphecy
   */
  protected $pluginType;

  /**
   * PluginInstance.
   *
   * @var TournamentInterface|ObjectProphecy
   */
  protected $tournamentPluginInstance;

  /**
   * The system under test.
   *
   * @var \Drupal\tournament\ParamConverter\TournamentPluginInstanceConverter
   */
  protected $sut;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->pluginManager  = $this->prophesize(PluginManagerInterface::class);
    $this->pluginTypeManager = $this->prophesize(PluginTypeManagerInterface::class);
    $this->tournamentPluginInstance = $this->prophesize(TournamentInterface::class);
    $this->pluginType = $this->prophesize(PluginTypeInterface::class);
  }

  /**
   * Prepare tests for ::convert.
   *
   * @param string $tournament_type
   */
  private function prepareConvertWithTournament($tournament_type){

    $this->pluginManager->hasDefinition($tournament_type)->willReturn(TRUE);
    $this->tournamentPluginInstance->getPluginId()->willReturn($tournament_type);
    $this->pluginManager->getInstance()->willReturn($this->tournamentPluginInstance->reveal());
    $this->pluginManager->createInstance($tournament_type)->willReturn($this->tournamentPluginInstance->reveal());
    $this->pluginType->getPluginManager()->willReturn($this->pluginManager->reveal());
    $this->pluginTypeManager->getPluginType('tournament')->willReturn($this->pluginType->reveal());

    $this->sut = new TournamentPluginInstanceConverter($this->pluginTypeManager->reveal());
  }

  /**
   * @covers ::convert
   */
  public function testConvertWithValidTournamentTypeToString(){
    $this->prepareConvertWithTournament('foo');

    $definition = [
      'type' => 'tournament_plugin_instance',
      'to_string' => TRUE,
    ];
    $this->assertSame($this->sut->convert('foo',$definition, '', []), 'foo');
  }

  /**
   * @covers ::convert
   */
  public function testConvertWithValidTournamentTypeToPluginInstance(){
    $this->prepareConvertWithTournament('foo');
    $definition = [
      'type' => 'tournament_plugin_instance',
      'to_string' => FALSE,
    ];
    $this->assertSame($this->sut->convert('foo', $definition, '',[]), $this->tournamentPluginInstance->reveal());
  }

  /**
   * @covers ::convert
   */
  public function testConvertWithNonExistingTournamentType(){
    $this->pluginManager->hasDefinition('foo')->willReturn(FALSE);
    $this->pluginType->getPluginManager()->willReturn($this->pluginManager->reveal());
    $this->pluginType->getPluginManager()->willReturn($this->pluginManager->reveal());
    $this->pluginTypeManager->getPluginType('tournament')->willReturn($this->pluginType->reveal());
    $this->sut = new TournamentPluginInstanceConverter($this->pluginTypeManager->reveal());

    $definition = [
      'type' => 'tournament_plugin_instance',
    ];
    $this->assertNull($this->sut->convert('foo', $definition,'',[]));
  }

  /**
   * @covers ::convert
   */
  public function testConvertWithInvalidPluginType(){
    $this->pluginManager->hasDefinition('foo')->willReturn(FALSE);
    $this->pluginType->getPluginManager()->willReturn($this->pluginManager->reveal());
    $this->pluginType->getPluginManager()->willReturn($this->pluginManager->reveal());
    $this->pluginTypeManager->getPluginType('tournament')->willThrow(\Exception::class);
    $this->sut = new TournamentPluginInstanceConverter($this->pluginTypeManager->reveal());

    $definition = [
      'type' => 'tournament_plugin_instance',
    ];
    $this->assertNull($this->sut->convert('foo', $definition,'',[]));
  }

  /**
   * @covers ::applies
   */
  public function testApplyWithValidDefinition(){
    $definition = [
      'type' => 'tournament_plugin_instance',
    ];
    $this->sut = new TournamentPluginInstanceConverter($this->pluginTypeManager->reveal());
    $route = $this->prophesize(Route::class);
    $this->assertTrue($this->sut->applies($definition, 'foo', $route->reveal()));
  }
}
