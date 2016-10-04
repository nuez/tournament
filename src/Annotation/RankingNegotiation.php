<?php

namespace Drupal\tournament\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Ranking negotiation annotation object.
 *
 * Plugin Namespace: Plugin\RankingNegotiation
 *
 * @Annotation
 */
class RankingNegotiation extends Plugin {

  /**
   * The language negotiation plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The default weight of the ranking negotiation plugin.
   *
   * @var int
   */
  public $weight;

  /**
   * The human-readable name of the ranking negotiation plugin.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $name;

  /**
   * The description of the ranking negotiation plugin.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $description;

}
