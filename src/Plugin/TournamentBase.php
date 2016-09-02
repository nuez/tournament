<?php

/**
 * @file
 * Contains \Drupal\tournament\Plugin\TournamentBase.
 */

namespace Drupal\tournament\Plugin;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Base class for Tournament plugins.
 */
abstract class TournamentBase extends PluginBase implements TournamentInterface {
  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['this_is_a_base_element'] = array(
      '#markup' => t('This is a base form element for the plugin.'),
    );
    return $form;
  }

}
