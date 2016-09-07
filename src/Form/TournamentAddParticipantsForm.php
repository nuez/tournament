<?php

/**
 * @file
 * Contains \Drupal\tournament\Form\TournamentForm.
 */

namespace Drupal\tournament\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\Entity;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\tournament\Entity\Participant;
use Drupal\tournament\Entity\ParticipantListBuilder;
use Drupal\tournament\Entity\TournamentInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for Tournament edit forms.
 *
 * @ingroup tournament
 */
class TournamentAddParticipantsForm extends ContentEntityForm {

  protected $entityQuery;

  /**
   * @inheritdoc;
   */
  public function __construct(EntityManagerInterface $entity_manager, QueryFactory $entity_query) {
    $this->entityQuery = $entity_query;
    parent::__construct($entity_manager);
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager'),
      $container->get('entity.query')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, TournamentInterface $tournament = NULL) {

    $form['participant_list'] = $this->buildParticipantsList();

    $form['participant'] = [
      '#type' => 'entity_autocomplete',
      '#title' => t('Select a participant'),
      '#required' => TRUE,
    ];

    switch ($tournament->getParticipantType()) {
      case 'user':
        $form['participant'] += [
          '#target_type' => 'user',
          '#description' => $this->t('Type in the username you want to add as a participant.'),
          '#selection_settings' => ['include_anonymous' => FALSE],
        ];
        break;
      case 'tournament_team':
        $form['participant'] += [
          '#target_type' => 'tournament_team',
          '#description' => $this->t('Type in the team name you want to add as a participant.'),
        ];
        break;
    }

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * Create list of added participants.
   */
  public function buildParticipantsList() {
    $tournament = $this->getEntity();
    $query = $this->entityQuery->get('tournament_participant')
      ->condition('tournament_reference', $tournament->id());
    $result = $query->execute();
    if (!empty($result)) {
      $header = [
        $this->t('Name'),
      ];
      $rows = [];
      foreach (array_keys($result) as $participant_id) {
        /** @var Participant $participant */
        $participant = $this->entityManager->getStorage('tournament_participant')
          ->load($participant_id);
        $rows[$participant_id]['name'] = $participant->getName();
      }
      $build = [
        '#type' => 'table',
        '#header' => $header,
        '#rows' => $rows,
      ];
      return $build;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $participant_id = $form_state->getValue('participant');

    /** @var TournamentInterface $tournament */
    $tournament = $this->getEntity();
    $participant_type = $tournament->getParticipantType();
    /** @var Participant $participant */
    $participant = $this->entityManager->getStorage('tournament_participant')
      ->create([
        'type' => $participant_type,
      ]);
    $participant_entity = $this->entityManager->getStorage($participant_type)
      ->load($participant_id);

    $participant->set('type', $participant_type)
      ->set($participant_type . '_reference', $participant_id)
      ->set('tournament_reference', $tournament->id())
      ->set('name', $participant_entity->label())
      ->save();

  }

  /**
   * Don't validate as we're not saving a entity.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }
}

