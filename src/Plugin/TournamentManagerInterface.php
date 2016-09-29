<?php
/**
 * @file
 *  Contains Drupal\tournament\Plugin\TournamentManagerInterface
 */

namespace Drupal\tournament\Plugin;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\tournament\Entity\Tournament;

interface TournamentManagerInterface extends ContainerFactoryPluginInterface{

  /**
   * @param \Drupal\tournament\Entity\Tournament $tournament
   * @return \Drupal\tournament\Plugin\Match[] .
   */
  public function getMatches(Tournament $tournament);

  /**
   * @param \Drupal\tournament\Entity\Tournament $tournament
   * @return \Drupal\tournament\Plugin\Participant[] .
   */
  public function getParticipants(Tournament $tournament);



  /**
   * @param \Drupal\tournament\Entity\Tournament $tournament
   * @return mixed
   */
  public function hasParticipants(Tournament $tournament);
}