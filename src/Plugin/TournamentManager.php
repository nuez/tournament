<?php

/**
 * @file
 * Contains \Drupal\tournament\Plugin\TournamentManager.
 */

namespace Drupal\tournament\Plugin;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Provides the Tournament plugin manager.
 */
class TournamentManager extends DefaultPluginManager {

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
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/Tournament', $namespaces, $module_handler, 'Drupal\tournament\Plugin\TournamentInterface', 'Drupal\tournament\Annotation\Tournament');

    $this->alterInfo('tournament_tournament_info');
    $this->setCacheBackend($cache_backend, 'tournament_tournament_plugins');
  }

}
