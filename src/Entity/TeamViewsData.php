<?php

namespace Drupal\tournament\Entity;

use Drupal\views\EntityViewsData;
use Drupal\views\EntityViewsDataInterface;

/**
 * Provides Views data for Team entities.
 */
class TeamViewsData extends EntityViewsData implements EntityViewsDataInterface {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['tournament_team']['table']['base'] = array(
      'field' => 'id',
      'title' => $this->t('Team'),
      'help' => $this->t('The Team ID.'),
    );

    return $data;
  }

}
