<?php

namespace Drupal\tournament\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface for defining Match entities.
 *
 * @ingroup tournament
 */
interface MatchInterface extends ContentEntityInterface, EntityChangedInterface {

  /**
   * @return Participant[].
   */
  public function getParticipants();
}
