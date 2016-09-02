<?php

/**
 * @file
 * Contains \Drupal\tournament\Entity\Tournament.
 */

namespace Drupal\tournament\Entity;

use Drupal\views\EntityViewsData;
use Drupal\views\EntityViewsDataInterface;

/**
 * Provides Views data for Tournament entities.
 */
class TournamentViewsData extends EntityViewsData implements EntityViewsDataInterface {
  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['tournament']['table']['base'] = array(
      'field' => 'id',
      'title' => $this->t('Tournament'),
      'help' => $this->t('The Tournament ID.'),
    );

    return $data;
  }

}
