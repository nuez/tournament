<?php

namespace Drupal\tournament\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface for defining Participant entities.
 *
 * @ingroup tournament
 */
interface ParticipantInterface extends ContentEntityInterface, EntityChangedInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Participant name.
   *
   * @return string
   *   Name of the Participant.
   */
  public function getName();

  /**
   * Sets the Participant name.
   *
   * @param string $name
   *   The Participant name.
   *
   * @return \Drupal\tournament\Entity\ParticipantInterface
   *   The called Participant entity.
   */
  public function setName($name);

  /**
   * Gets the Participant creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Participant.
   */
  public function getCreatedTime();

  /**
   * Sets the Participant creation timestamp.
   *
   * @param int $timestamp
   *   The Participant creation timestamp.
   *
   * @return \Drupal\tournament\Entity\ParticipantInterface
   *   The called Participant entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Participant published status indicator.
   *
   * Unpublished Participant are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Participant is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Participant.
   *
   * @param bool $published
   *   TRUE to set this Participant to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\tournament\Entity\ParticipantInterface
   *   The called Participant entity.
   */
  public function setPublished($published);

}
