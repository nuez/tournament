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
    if ('p_type' == $field_definition->getName() && $operation != 'view') {
      /**
       * @var TournamentInterface $entity
       */
      $entity = $items->getEntity();
      if($entity->hasParticipants()){
        return AccessResult::forbidden();
      }
    }

    return parent::fieldAccess($operation, $field_definition, $account, $items, $return_as_object);
  }
}
