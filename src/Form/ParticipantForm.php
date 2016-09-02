<?php

namespace Drupal\tournament\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Participant edit forms.
 *
 * @ingroup tournament
 */
class ParticipantForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    
    /* @var $entity \Drupal\tournament\Entity\Participant */
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
        drupal_set_message($this->t('Created the %label Participant.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Participant.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.participant.canonical', ['participant' => $entity->id()]);
  }

}
