<?php

/**
 * @file
 * Contains \Drupal\tournament\Plugin\TournamentBase.
 */

namespace Drupal\tournament\Plugin;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Entity\EntityManager;
use Drupal\Core\Form\FormStateInterface;

/**
 * Base class for Tournament plugins.
 */
abstract class TournamentBase extends PluginBase implements TournamentInterface {

  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    $this->configuration = $configuration;
    $this->pluginId = $plugin_id;
    $this->pluginDefinition = $plugin_definition;
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['this_is_a_base_element'] = array(
      '#markup' => t('This is a base form element for the plugin.'),
    );
    return $form;
  }

  /**
   * @return mixed array of Match entities.
   */
  public function getMatches() {
    return 'tset';
  }

}
