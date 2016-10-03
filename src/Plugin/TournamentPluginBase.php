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
use Drupal\tournament\Entity\Match;
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


  /**
   * {@inheritdoc}
   *
   * @throws \InvalidArgumentException
   *   When $results is not an array of integers that matches the amount of
   *   associated MatchResult entities.
   *
   * @throws \Exception
   *   When the status of the match is not confirmed yet or already been
   *   processed.
   */
  public function processMatchResult(Match $match, $results) {

    // Check the format of the $result parameter.
    if (!is_array($results)) {
      throw new \InvalidArgumentException('The result must be an array of integers.');
    }

    foreach ($results as $result) {
      if (!is_numeric($result)) {
        throw new \InvalidArgumentException('The result must be an array of integers.');
      }
    }

    // Check if the amount of results matches the amount of MatchResult entities.
    if(count($match->getMatchResults()) != count($results)){
      throw new \InvalidArgumentException('The amount of MatchResults has to 
      match the amount of results passed as an argument.');
    }

    // Check if the Match result can be processed according to the status.
    if (Match::PROCESSED == $match->getStatus()) {
      throw new \Exception('The match result has already been processed.');
    }
    if (Match::CONFIRMED != $match->getStatus()) {
      throw new \Exception('The match result can only be processed when the result is confirmed.');
    }

    // Save the state of the Match to 'Processed'.
    $match->set('status', Match::PROCESSED)->save();
  }
}
