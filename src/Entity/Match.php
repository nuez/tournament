<?php

namespace Drupal\tournament\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Match entity.
 *
 * @ingroup tournament
 *
 * @ContentEntityType(
 *   id = "tournament_match",
 *   label = @Translation("Match"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "views_data" = "Drupal\tournament\Entity\EntityViewsData",
 *     "access" = "Drupal\tournament\Entity\MatchAccessControlHandler",
 *   },
 *   base_table = "tournament_match",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "status" = "status",
 *   },
 * )
 */
class Match extends ContentEntityBase implements MatchInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the Match entity.'))
      ->setReadOnly(TRUE);
    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Match entity.'))
      ->setReadOnly(TRUE);

    $fields['match_results'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Match result'))
      ->setDescription(t('A reference to a match result entity.'))
      ->setRevisionable(FALSE)
      ->setSetting('target_type', 'tournament_match_result')
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setTranslatable(FALSE);

    $fields['match_date'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('Match date'))
      ->setDescription(t('Play date of the match'))
      ->setRevisionable(FALSE)
      ->setTranslatable(FALSE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['tournament_reference'] = BaseFieldDefinition::create('entity_reference')
      ->setSetting('target_type', 'tournament')
      ->setLabel(t('Tournament reference'))
      ->setDescription(t('The tournament this match belongs to.'));

    return $fields;
  }

}
