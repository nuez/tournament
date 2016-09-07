<?php

/**
 * @file
 * Contains Drupal\tournament\Controller\TournamentAddController.
 */

namespace Drupal\tournament\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\plugin\PluginType\PluginTypeManagerInterface;
use Drupal\tournament\Plugin\TournamentBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Class TournamentAddController.
 *
 * @property \Drupal\Core\Entity\EntityStorageInterface storage
 *
 * @package Drupal\tournament\Controller
 */
class TournamentController extends ControllerBase {

  /**
   * PluginTypeManager
   *
   * @var \Drupal\plugin\PluginType\PluginTypeManagerInterface
   */
  protected $pluginTypeManager;

  /**
   * TournamentController constructor.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   * @param \Drupal\plugin\PluginType\PluginTypeManagerInterface $plugin_type_manager
   */
  public function __construct(EntityStorageInterface $storage, PluginTypeManagerInterface $plugin_type_manager) {
    $this->storage = $storage;
    $this->pluginTypeManager = $plugin_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /** @var EntityTypeManagerInterface $entity_type_manager */
    $entity_type_manager = $container->get('entity_type.manager');
    return new static(
      $entity_type_manager->getStorage('tournament'),
      $container->get('plugin.plugin_type_manager')
    );
  }

  public function view() {
    return array('#markup' => t('@todo show tournamnet'));
  }

  public function title() {
    return $this->t('Tournament types');
  }

  /**
   * Presents the creation form for tournament entities of given bundle/type.
   *
   * @param \Drupal\tournament\Plugin\TournamentBase $plugin
   * @return array A form array as expected by drupal_render().
   * A form array as expected by drupal_render().
   * @internal param \Symfony\Component\HttpFoundation\Request $request
   */
  public function addForm($plugin) {
    $entity = $this->storage->create(array(
      'type' => $plugin->getPluginId(),
    ));
    return $this->entityFormBuilder()->getForm($entity);
  }

  public function settings($bundle) {
    return array('#markup' => '@todo settings');
  }

  public function getAddFormTitle(TournamentBase $plugin){
    return '@todo title';
  }

  /**
   * Returns a list of available tournament types.
   *
   * @return array A render array.
   */
  public function add() {

    $tournamentTypes = $this->pluginTypeManager->getPluginType('tournament')
      ->getPluginManager()
      ->getDefinitions();

    if(empty($tournamentTypes)){
      return array('#markup' => t('Please enable a Tournament type by enabling
      one of the available submodules.'));
    }

    $items = [];

    foreach ($tournamentTypes as $type) {
      /** @var Url $url */
      $url = Url::fromRoute('tournament.add_form', array('plugin' => $type['id']));
      /** @var TranslatableMarkup $label */
      $label = $type['label'];
      $items[] = Link::fromTextAndUrl($label->render(), $url);
    }
    return [
      '#theme' => 'item_list',
      '#type' => 'ul',
      '#items' => $items,
      '#attributes' => [],
    ];
  }
}
