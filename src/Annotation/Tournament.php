<?php

/**
 * @file
 * Contains \Drupal\tournament\Annotation\Tournament.
 */

namespace Drupal\tournament\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Tournament item annotation object.
 *
 * @see \Drupal\tournament\Plugin\TournamentManager
 * @see plugin_api
 *
 * @Annotation
 */
class Tournament extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The label of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

}
