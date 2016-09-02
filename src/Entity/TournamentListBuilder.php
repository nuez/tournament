<?php

/**
 * @file
 * Contains \Drupal\tournament\TournamentListBuilder.
 */

namespace Drupal\tournament\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Routing\LinkGeneratorTrait;
use Drupal\Core\Url;
use Drupal\tournament\Entity\Tournament;
use Drupal\tournament\Plugin\TournamentBase;
use Drupal\tournament\Plugin\TournamentManager;

/**
 * Defines a class to build a listing of Tournament entities.
 *
 * @ingroup tournament
 */
class TournamentListBuilder extends EntityListBuilder {

  use LinkGeneratorTrait;


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Tournament ID');
    $header['type'] = $this->t('Tournament type');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity Tournament */
    /** @var $type TournamentBase */
    $row['id'] = $entity->id();
    $type = $entity->getType();
    /** @var TournamentManager $plugin_type */
    $plugin_type = \Drupal::service('plugin.manager.tournament.manager');
    $row['type'] = $plugin_type->getDefinition($type)['label']->render();
    $row['name'] = $this->l(
      $entity->label(),
      new Url(
        'entity.tournament.edit_form', array(
          'tournament' => $entity->id(),
        )
      )
    );

    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function getOperations(EntityInterface $entity) {
    $operations = parent::getOperations($entity);
    $operations['add_participants'] = [
      'title' => $this->t('Add participants'),
      'weight' => 2,
      'url' => Url::fromRoute('entity.tournament.add_participants', ['tournament' => $entity->id()]),
    ];
    return $operations;
  }

}
