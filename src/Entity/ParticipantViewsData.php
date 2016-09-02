<?php

namespace Drupal\tournament\Entity;

use Drupal\views\EntityViewsData;
use Drupal\views\EntityViewsDataInterface;

/**
 * Provides Views data for Participant entities.
 */
class ParticipantViewsData extends EntityViewsData implements EntityViewsDataInterface {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['tournament_participant']['table']['base'] = array(
      'field' => 'id',
      'title' => $this->t('Participant'),
      'help' => $this->t('The Participant ID.'),
    );

    return $data;
  }

}
