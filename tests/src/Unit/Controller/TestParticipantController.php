<?php

/**
 * @file
 * Contains Drupal\Tests\tournament\Controller\TestParticipantController.
 */

namespace Drupal\Tests\tournament\Unit\Controller;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\tournament\Controller\ParticipantController;


/**
 * Class TournamentAddController.
 *
 * @property \Drupal\Core\Entity\EntityStorageInterface storage
 *
 * @package Drupal\tournament\Controller
 */
class TestParticipantController extends ParticipantController {

  public function __construct(EntityStorageInterface $storage) {
    parent::__construct($storage);
  }

  public function t($string, array $args = array(), array $options = array()) {
    return 'test';
  }
}
