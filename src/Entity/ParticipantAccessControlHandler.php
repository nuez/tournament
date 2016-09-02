<?php

namespace Drupal\tournament\Entity;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Participant entity.
 *
 * @see \Drupal\tournament\Entity\Participant.
 */
class ParticipantAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\tournament\ParticipantInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished tournament participant entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published tournament participant entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit tournament participant entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete tournament participant entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add tournament participant entities');
  }

}
