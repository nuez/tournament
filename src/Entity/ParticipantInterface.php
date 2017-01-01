<?php

namespace Drupal\tournament\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\Entity\User;

/**
 * Provides an interface for defining Participant entities.
 *
 * @ingroup tournament
 */
interface ParticipantInterface extends ContentEntityInterface, EntityChangedInterface {

  /**
   * @return Tournament
   */
  public function getTournament();

  /**
   * @return string
   */
  public function getParticipantType();

  /**
   * @return User|Teams
   */
  public function getReferencedEntity();
}
