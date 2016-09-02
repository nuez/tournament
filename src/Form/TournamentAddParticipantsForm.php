<?php

/**
 * @file
 * Contains \Drupal\tournament\Form\TournamentForm.
 */

namespace Drupal\tournament\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\Entity;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\tournament\Entity\TournamentInterface;

/**
 * Form controller for Tournament edit forms.
 *
 * @ingroup tournament
 */
class TournamentAddParticipantsForm extends ContentEntityForm {

  protected $participantManager;

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, TournamentInterface $tournament = NULL) {

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
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $participant_id = $form_state->getValue('participant');
    /**
     * @var TournamentInterface $tournament
     */
    $tournament = $this->getEntity();
    //$participant = $this->entityManager->getStorage($tournament->getParticipantType())->load($participant_id);
    $participant = $this->entityManager->getStorage('tournament_participant')->create([
      'type' => $tournament->getParticipantType(),
      ''
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }
}
