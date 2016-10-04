<?php


namespace Drupal\tournament_round_robin\Plugin\Tournament;


use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\tour\Entity\Tour;
use Drupal\tournament\Entity\Match;
use Drupal\tournament\Entity\MatchResult;
use Drupal\tournament\Entity\Participant;
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
    $matchId = $match->set('match_results', [
      $matchResultHome->id(),
      $matchResultAway->id()
    ])->save();

    return $entityTypeManager->getStorage('tournament_match')->load($matchId);
  }

  /**
   * {@inheritdoc}
   *
   * Update MatchResult entities linked to the match and update Tournament
   * Participants.
   */
  public function processMatchResult(Match $match, $results) {
    parent::processMatchResult($match, $results);
    if ($results[0] == $results[1]) {
      $this->processMatchDraw($match, $results);
    }
    else {
      $this->processMatchWin($match, $results);
    }
  }

  /**
   * Processes a match that was drawn.
   *
   * @param \Drupal\tournament\Entity\Match $match
   * @param $results
   */
  private function processMatchDraw(Match $match, $results) {
    $matchResults = $match->getMatchResults();
    $tournament = $match->get('tournament_reference')->referencedEntities()[0];
    $config = $tournament->getConfig();

    foreach($matchResults as $key => $matchResult){
      /** @var MatchResult $matchResult */
      $matchResult->set('score', $results[$key])->save();
      $participant = $matchResult->get('participant')->referencedEntities()[0];
      $participant->set('draw', $participant->get('draw')->getString() + 1);
      $score = $results[$key];
      $participant->set('points', $participant->get('points')->getString() + $config['points_draw']);
      $participant->set('score_for', $participant->get('score_for')->getString() + $score);
      $participant->set('score_against', $participant->get('score_against')->getString() + $score);
      $participant->save();
    }
  }

  /**
   * Processes a Match that was won/lost.
   *
   * @param \Drupal\tournament\Entity\Match $match
   * @param $results
   */
  private function processMatchWin(Match $match, $results) {

    $matchResults = $match->getMatchResults();

    // Sort the array of results so the highest result goes first.
    arsort($results);

    /** @var Tournament $tournament */
    $tournament = $match->get('tournament_reference')->referencedEntities()[0];
    $config = $tournament->getConfig();

    $i = 0;
    foreach ($results as $key => $score) {
      /** @var MatchResult $matchResult */
      $matchResult = $matchResults[$key];
      $matchResult->set('score', $results[$key])->save();
      // If this is the first element, then this is the winner.
      $participant = $matchResult->get('participant')
        ->referencedEntities()[0];

      if ($i == 0) { // Handle the winner first
        /** @var Participant $participant */
        $participant->set('win', $participant->get('win')->getString() + 1);
        $participant->set('points', $participant->get('points')
            ->getString() + $config['points_win']);
        $participant->set('score_for', $participant->get('score_for')
            ->getString() + ($key == 0 ? $results[0] : $results[1]));
        $participant->set('score_against', $participant->get('score_against')
            ->getString() + ($key == 0 ? $results[1] : $results[0]));
      }
      elseif ($i == 1) {
        /** @var Participant $participant */
        $participant->set('loss', $participant->get('win')->getString() + 1);
        $participant->set('points', $participant->get('points')
            ->getString() + $config['points_loss']);
        $participant->set('score_for', $participant->get('score_for')
            ->getString() + ($key == 0 ? $results[0] : $results[1]));
        $participant->set('score_against', $participant->get('score_against')
            ->getString() + ($key == 0 ? $results[1] : $results[0]));

      }
      $participant->save();
      $i++;
    }
  }
}