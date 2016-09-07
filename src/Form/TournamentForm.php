<?php

/**
 * @file
 * Contains \Drupal\tournament\Form\TournamentForm.
 */

namespace Drupal\tournament\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\tournament\Plugin\TournamentManager;

/**
 * Form controller for Tournament edit forms.
 *
 * @ingroup tournament
 */
class TournamentForm extends ContentEntityForm {
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\tournament\Entity\Tournament */
    $form = parent::buildForm($form, $form_state);
    $entity = $this->entity;
    $bundle = $entity->bundle();

    /** @var TournamentManager $plugin_type */
    $plugin_type = \Drupal::service('plugin.manager.tournament.manager');

    $plugin = $plugin_type->createInstance($bundle);

    $form['data'] = $plugin->buildConfigurationForm(array() , $form_state);
    
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $bundle = $entity->bundle();
    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Tournament.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Tournament.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.tournament.canonical', ['tournament' => $entity->id()]);
  }

}
