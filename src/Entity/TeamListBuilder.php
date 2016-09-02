<?php

namespace Drupal\tournament\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Routing\LinkGeneratorTrait;
use Drupal\Core\Url;
use Drupal\tournament\Entity\Team;

/**
 * Defines a class to build a listing of Team entities.
 *
 * @ingroup tournament
 */
class TeamListBuilder extends EntityListBuilder {

  use LinkGeneratorTrait;

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Team ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity Team */
    $row['id'] = $entity->id();
    $row['name'] = $this->l(
      $entity->label(),
      new Url(
        'entity.tournament_team.edit_form', array(
          'tournament_team' => $entity->id(),
        )
      )
    );
    return $row + parent::buildRow($entity);
  }

}
