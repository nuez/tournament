<?php

/**
 * @file
 * Contains \Drupal\tournament\TournamentInterface.
 */

namespace Drupal\tournament\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Tournament entities.
 *
 * @ingroup tournament
 */
interface TournamentInterface extends ContentEntityInterface, EntityChangedInterface {

  /**
   * @return int Status of the Tournament
   */
  public function getStatus();

  /**
   * @return string Participant Type
   */
  public function getParticipantType();

  /**
   * @return string Tournament Type
   */
  public function getTournamentType();

  /**
   * @return AccountInterface
   */
  public function getAuthor();

}
