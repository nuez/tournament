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
use Drupal\tournament\Entity\Participant;
use Drupal\tournament\Entity\Team;
use Drupal\tournament\Entity\Tournament;
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

  /**
   * Add the selected array of Users or Teams ot the Tournament.
   *
   * @param \Drupal\tournament\Entity\Tournament $tournament
   * @param \Drupal\tournament\Entity\Team[]|\Drupal\user\Entity\User[] $participants
   *
   * @return Participant[]
   */
  public function addParticipants(Tournament $tournament, $participants){
    $participantEntities = [];
    foreach($participants as $participant){
      $participantEntity = Participant::create([
        'type' => $participant->getEntityTypeId(),
        'tournament_reference' => $tournament,
        'name' => $participant->label(),
        'created' => REQUEST_TIME,
        'changed' => REQUEST_TIME,
        'points' => 0,
        'wins' => 0,
        'draw' => 0,
        'loss' => 0,
        'score_for' => 0,
        'score_against' => 0,
        $participant->getEntityTypeId() .'_reference' => $participant->id(),
      ]);
      $participantEntity->save();
      $participantEntities[] = $participantEntity;
    }
    return $participantEntities;
  }

  /**
   * @param \Drupal\tournament\Entity\Tournament $tournament
   * @return mixed|void
   */
  public function startTournament(Tournament $tournament) {
    $tournament->set('status', Tournament::STATUS_STARTED);
    $tournament->save();
  }
}
