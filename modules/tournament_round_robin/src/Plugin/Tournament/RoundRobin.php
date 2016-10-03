<?php


namespace Drupal\tournament_round_robin\Plugin\Tournament;


use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\tournament\Entity\Match;
use Drupal\tournament\Entity\Tournament;
use Drupal\tournament\Entity\TournamentInterface;
use Drupal\tournament\Plugin\TournamentPluginBase;
use Zend\Diactoros\MessageTrait;


/**
 * Plugin implementation of the 'example_field_type' field type.
 *
 * @Tournament(
 *   id = "round_robin",
 *   label = @Translation("Round Robin"),
 *   description = @Translation("Round Robin Tournament type") * )
 */
class RoundRobin extends TournamentPluginBase {


  use MessageTrait;


  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    return array();
  }


  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    /*$configuration += array(

    );
    return $this;
    */
  }


  public function validateConfigurationForm(array $form, FormStateInterface $form_state) {
    // TODO: Implement validateConfigurationForm() method.
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    $form['this_is_an_element'] = array(
      '#markup' => t('This is a form element for the plugin.'),
    );
    return $form;
  }


  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function getSummary() {
    return array('#markup' => 'summary');
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
  }


  /**
   * Sets the configuration().
   *
   * @param array
   */
  public function setConfig($config) {
    $this->set('config', $config);
  }

  /**
   * {@inheritdoc}
   */
  public function generateMatches(TournamentInterface $tournament) {

    $participants = $this->tournamentManager->getParticipants($tournament);

    $home_ids = [];
    $away_ids = [];

    array_walk($participants, function (&$value, $key) use (&$home_ids, &$away_ids) {
      $participant_id = $value->id();
      $home_ids[] = $participant_id;
      $away_ids[] = $participant_id;
    });

    $config = $tournament->getConfig();
    $rounds = $config['rounds'];
    $shuffle = $config['shuffle'];

    $tournament_matches = [];

    // Every round we iterate over the home and away participants,
    // starting with the home teams and knowing that participants
    // can't play themselves.
    for ($i = 0; $i < $rounds; $i++) {
      $matches_per_round = [];
      $items = [];

      // For every other round we reverse the array before the iteration,
      // to ensure that the participants that we're previously away, are
      // now home.
      $home_ids = $i % 2 != 0 ? array_reverse($home_ids) : $home_ids;

      foreach ($home_ids as $home_id) {
        foreach ($away_ids as $away_id) {
          if ($home_id != $away_id && !array_key_exists($away_id, $items)) {
            $items[$home_id][] = $away_id;
          }
        }
      }
      foreach ($items as $home_id => $item) {
        foreach ($item as $away_id) {
          $match = $this->createMatch($tournament, (int) $home_id, (int) $away_id);
          $matches_per_round[] = $match;
        }
      }

      // Revert back so the matches follow the same order, so that the participants
      // that played first in the first round, don't play last in the second round,
      // and vice versa. This is undone by the shuffle if activated.
      $matches_per_round = $i % 2 != 0 ? array_reverse($matches_per_round) : $matches_per_round;

      // When shuffle is true, shuffle the matches. Shuffle the matches per round
      // to avoid that the home and return match is played close to each other.
      if ($shuffle) {
        shuffle($matches_per_round);
      }

      foreach ($matches_per_round as $match) {
        $tournament_matches[] = $match;
      }
    }

    return $tournament_matches;
  }

  /**
   * Create match
   *
   * @param \Drupal\tournament\Entity\Tournament $tournament
   * @param $home_participant_id
   * @param $away_participant_id
   *
   * @return int Match ID
   */
  public function createMatch(Tournament $tournament, $home_participant_id, $away_participant_id) {
    $entityTypeManager = \Drupal::getContainer()->get('entity_type.manager');

    $matchResultHome = $entityTypeManager->getStorage('tournament_match_result')
      ->create([
        'participant' => $home_participant_id,
        'score' => 0,
      ]);
    $matchResultHome->save();

    $matchResultAway = $entityTypeManager->getStorage('tournament_match_result')
      ->create([
        'participant' => $away_participant_id,
        'score' => 0,
      ]);
    $matchResultAway->save();

    $match = $entityTypeManager->getStorage('tournament_match')
      ->create([
        'created' => REQUEST_TIME,
        'updated' => REQUEST_TIME,
        'tournament_reference' => $tournament->id(),
        'status' => Match::AWAITING_RESULT,
      ]);
    $matchId = $match->set('match_results', [$matchResultHome->id(), $matchResultAway->id()])->save();


    return $entityTypeManager->getStorage('tournament_match')->load($matchId);
  }

  /**
   * Process result
   */
}