<?php

namespace Drupal\tournament\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\tournament\Entity\Team;

/**
 * Form controller for Team edit forms.
 *
 * @ingroup tournament
 */
class TeamForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity Team */
    $form = parent::buildForm($form, $form_state);
    $entity = $this->entity;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Team.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Team.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.tournament_team.canonical', ['tournament_team' => $entity->id()]);
  }

}
