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
use Drupal\tournament\Entity\Tournament;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the Tournament plugin manager.
 */
class TournamentRankingManager extends DefaultPluginManager implements TournamentRankingManagerInterface {

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

}
