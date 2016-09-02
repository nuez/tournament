<?php

namespace Drupal\tournament\Entity;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\tournament\Entity\TeamInterface;

/**
 * Access controller for the Team entity.
 *
 * @see \Drupal\tournament\Entity\Team.
 */
class TeamAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var TeamInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished team entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published team entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit team entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete team entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add team entities');
  }

}
