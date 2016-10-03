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
class ParticipantController extends ControllerBase {


  /**
   * TournamentController constructor.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   */
  public function __construct(EntityStorageInterface $storage) {
    $this->storage = $storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /** @var EntityTypeManagerInterface $entity_type_manager */
    $entity_type_manager = $container->get('entity_type.manager');
    return new static(
      $entity_type_manager->getStorage('tournament_participant')
    );
  }

  public function view() {
    return array('#markup' => $this->t('String'));
  }

  public function title() {
    return $this->t('Tournament types');
  }

  /**
   * Presents the creation form for tournament entities of given bundle/type.
   *
   * @param $bundle
   * @return array A form array as expected by drupal_render().
   * A form array as expected by drupal_render().
   * @internal param \Symfony\Component\HttpFoundation\Request $request
   */
  public function addForm($bundle) {
    $entity = $this->storage->create(array(
      'type' => $bundle,
    ));
    return $this->entityFormBuilder()->getForm($entity);
  }

  public function settings($bundle) {
    return array('#markup' => $bundle);
  }

  public function getAddFormTitle(TournamentBase $plugin){
    return 'title';
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
      '#title' => '',
    ];

  }

}
