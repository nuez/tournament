<?php


namespace Drupal\tournament_round_robin\Plugin\Tournament;


use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\tournament\Plugin\TournamentBase;
use Zend\Diactoros\MessageTrait;


/**
 * Plugin implementation of the 'example_field_type' field type.
 *
 * @Tournament(
 *   id = "round_robin",
 *   label = @Translation("Round Robin"),
 *   description = @Translation("Round Robin Tournament type") * )
 */
class RoundRobin extends TournamentBase{

  use StringTranslationTrait;

  use MessageTrait;

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    return array(
    );
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


  public function validateConfigurationForm(array $form, FormStateInterface $form_state)
  {
    // TODO: Implement validateConfigurationForm() method.
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form , $form_state);
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
    return array(
    );
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    // $this->configuration['height'] = $form_state->getValue('height');
    // $this->configuration['width'] = $form_state->getValue('width');
  }

  public function generateMatches()
  {
  }
}