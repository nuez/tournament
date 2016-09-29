<?php

/**
 * @file
 * Contains \Drupal\tournament\Plugin\TournamentBase.
 */

namespace Drupal\tournament\Plugin;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Entity\EntityManager;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\ContextAwarePluginAssignmentTrait;
use Drupal\Core\Plugin\ContextAwarePluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base class for Tournament plugins.
 */
abstract class TournamentPluginBase extends ContextAwarePluginBase implements TournamentPluginInterface, ContainerFactoryPluginInterface {


  use ContextAwarePluginAssignmentTrait;

  /**
   * @var TournamentManagerInterface $tournamentManager;
   */
  protected $tournamentManager;

  /**
   * TournamentPluginBase constructor.
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @param \Drupal\tournament\Plugin\TournamentManagerInterface $tournament_manager
   */

  public function __construct(array $configuration, $plugin_id, $plugin_definition, TournamentManagerInterface $tournament_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->tournamentManager = $tournament_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.tournament.manager')
    );
  }

  /**
   * {@inheritdoc}
   *
   * @todo This is a stub.
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['this_is_a_base_element'] = array(
      '#markup' => t('This is a base form element for the plugin.'),
    );
    return $form;
  }

}
