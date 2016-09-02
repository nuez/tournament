<?php

/**
 * @file
 * Contains \Drupal\tournament\TournamentInterface.
 */

namespace Drupal\tournament\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Tournament entities.
 *
 * @ingroup tournament
 */
interface TournamentInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {
  // Add get/set methods for your configuration properties here.
  /**
   * Gets the Tournament type.
   *
   * @return string
   *   The Tournament type.
   */

  /**
   * Gets the Tournament name.
   *
   * @return string
   *   Name of the Tournament.
   */
  public function getName();

  /**
   * Sets the Tournament name.
   *
   * @param string $name
   *   The Tournament name.
   *
   * @return TournamentInterface
   *   The called Tournament entity.
   */
  public function setName($name);

  /**
   * Gets the Tournament creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Tournament.
   */
  public function getCreatedTime();

  /**
   * Sets the Tournament creation timestamp.
   *
   * @param int $timestamp
   *   The Tournament creation timestamp.
   *
   * @return TournamentInterface
   *   The called Tournament entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Tournament published status indicator.
   *
   * Unpublished Tournament are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Tournament is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Tournament.
   *
   * @param bool $published
   *   TRUE to set this Tournament to published, FALSE to set it to unpublished.
   *
   * @return TournamentInterface
   *   The called Tournament entity.
   */
  public function setPublished($published);

  /**
   * Gets the started status of the tournament.
   *
   * @return bool $started
   *  TRUE if the Tournament has started.
   */
  public function getStarted();

  /**
   * See if Tournament has participants.
   */
  public function hasParticipants();

  /**
   * Get Particpant Type
   */
  public function getParticipantType();

  /**
   * Sets the started status of the tournament.
   *
   * @param bool $started
   *   TRUE to set this tournament to started.
   *
   * @return TournamentInterface
   *  The called Tournament entity.
   */
  public function setStarted($started);


}
