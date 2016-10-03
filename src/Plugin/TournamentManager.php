<?php

/**
 * @file
 * Contains \Drupal\tournament\Plugin\TournamentManager.
 */

namespace Drupal\tournament\Plugin;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\tournament\Entity\Match;
use Drupal\tournament\Entity\Tournament;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the Tournament plugin manager.
 */
class TournamentManager extends DefaultPluginManager implements TournamentManagerInterface {

  /**
   * Array of stored participants per tournament id.
   *
   * @var mixed
   */
  private $participants;

  /**
   * @var QueryFactory $entityQuery
   */
  private $entityQuery;


  /**
   * @var EntityTypeManagerInterface $entityTypeManager
   */
  private $entityTypeManager;

  /**
   * Constructor for TournamentManager objects.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   * @param \Drupal\Core\Entity\Query\QueryFactory $entity_query
   *   The QueryFactory service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler, QueryFactory $entity_query, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct('Plugin/Tournament', $namespaces, $module_handler, 'Drupal\tournament\Plugin\TournamentPluginInterface', 'Drupal\tournament\Annotation\Tournament');
    $this->alterInfo('tournament_tournament_info');
    $this->setCacheBackend($cache_backend, 'tournament_tournament_plugins');
    $this->entityQuery = $entity_query;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('query.factory'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getMatches(Tournament $tournament) {
    $this->entityQuery->get('tournament_match')
      ->condition('tournament_reference', $tournament->id())
      ->execute();
  }

  /**
   * Get Participants.
   *
   * @param \Drupal\tournament\Entity\Tournament $tournament
   *
   * @return \Drupal\tournament\Plugin\Participant[]
   *
   * This query should only be executed once in the execution stack so
   * it needs to be clear if it has been executed before.
   *
   * @todo See if there is a more elegant way of setting this.
   */
  public function getParticipants(Tournament $tournament) {
    if (!$this->participants[$tournament->id()]) {
      $participant_ids = $this->entityQuery->get('tournament_participant')
        ->condition('tournament_reference', $tournament->id())
        ->execute();

      $participants = $this->entityTypeManager->getStorage('tournament_participant')
        ->loadMultiple($participant_ids);

      $this->participants[$tournament->id()] = $participants;
    }
    return $this->participants[$tournament->id()];
  }

  /**
   * See if tournament has participants.
   *
   * @param \Drupal\tournament\Entity\Tournament $tournament
   * @return bool
   */
  public function hasParticipants(Tournament $tournament) {
    $query = $this->entityQuery('tournament_participant')
      ->condition('tournament_reference', $tournament->id());
    $result = $query->count()->execute();
    return $result != 0;
  }

}
