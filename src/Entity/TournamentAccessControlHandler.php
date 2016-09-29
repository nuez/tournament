<?php

/**
 * @file
 * Contains \Drupal\tournament\TournamentAccessControlHandler.
 */

namespace Drupal\tournament\Entity;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Tournament entity.
 *
 * @see \Drupal\tournament\Entity\Tournament.
 */
class TournamentAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var TournamentInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished tournament entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published tournament entities');

      case 'add_participants':
        if (!$entity->getStarted()) {
          return AccessResult::allowedIfHasPermission($account, 'edit tournament entities');
        }
        else {
          return AccessResult::forbidden();
        }
      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit tournament entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete tournament entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add tournament entities');
  }

  /**
   * {@inheritdoc}
   *
   * Disallow changing the tournament participant type when  participants
   * have been added.
   */
  public function fieldAccess($operation, FieldDefinitionInterface $field_definition, AccountInterface $account = NULL, FieldItemListInterface $items = NULL, $return_as_object = FALSE) {
    if ('participant_type' == $field_definition->getName() && $operation != 'view') {

      /**
       * @var TournamentInterface $entity
       */
      $tournament = $items->getEntity();

      // Get the Tournament Manager Service. Since the Control Handler doesn't
      // have access to the Dependency Injection Container we have to
      // access it through the Static Service Container Wrapper.
      $tournamentManager = \Drupal::getContainer()->get('plugin.manager.tournament.manager');
      if($tournamentManager->hasParticipants($tournament)){
        return AccessResult::forbidden();
      }
    }

    return parent::fieldAccess($operation, $field_definition, $account, $items, $return_as_object);
  }
}
