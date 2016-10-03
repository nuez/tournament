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
*   id = "tournament_match_result",
*   label = @Translation("Match result"),
*   handlers = {
*     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
*     "views_data" = "Drupal\tournament\Entity\EntityViewsData",
*     "access" = "Drupal\tournament\Entity\MatchResultAccessControlHandler",
*   },
*   base_table = "tournament_match_result",
*   entity_keys = {
*     "id" = "id",
*     "uuid" = "uuid",
*   },
* )
*/
class MatchResult extends ContentEntityBase implements MatchResultInterface {

  use EntityChangedTrait;



  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the Match Result entity.'))
      ->setReadOnly(TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Match Result entity.'))
      ->setReadOnly(TRUE);

    $fields['participant'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Match participant'))
      ->setDescription(t('The user ID of author of the Team entity.'))
      ->setRevisionable(FALSE)
      ->setSetting('target_type', 'tournament_participant')
      ->setTranslatable(FALSE);

    $fields['score'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Match score'))
      ->setDescription(t('Match score'))
      ->setRevisionable(FALSE)
      ->setTranslatable(FALSE);

    return $fields;
  }

}