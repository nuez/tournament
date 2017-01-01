<?php

namespace Drupal\tournament\Entity;

use Drupal\Core\Config\Entity\Query\QueryFactory;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\EntityReferenceFieldItemList;
use Drupal\Core\Field\FieldDefinitionListenerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\tournament\Exception\TournamentException;

/**
 * Defines the Participant entity.
 *
 * @ingroup tournament
 *
 * @ContentEntityType(
 *   id = "tournament_participant",
 *   label = @Translation("Participant"),
 *   bundle_label = @Translation("Participant type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\tournament\Entity\ParticipantListBuilder",
 *     "views_data" = "Drupal\tournament\Entity\ParticipantViewsData",
 *     "form" = {
 *       "default" = "Drupal\tournament\Form\ParticipantForm",
 *       "add" = "Drupal\tournament\Form\ParticipantForm",
 *       "edit" = "Drupal\tournament\Form\ParticipantForm",
 *       "delete" = "Drupal\tournament\Form\ParticipantDeleteForm",
 *     },
 *     "access" = "Drupal\tournament\Entity\ParticipantAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\tournament\Entity\ParticipantHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "tournament_participant",
 *   admin_permission = "administer participant entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "bundle" = "type",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "canonical" = "/admin/tournament/participant/{tournament_participant}",
 *     "add-form" = "/admin/tournament/participant/add",
 *     "edit-form" = "/admin/tournament/participant/{tournament_participant}/edit",
 *     "delete-form" = "/admin/tournament/participant/{tournament_participant}/delete",
 *     "collection" = "/admin/tournament/participant",
 *   },
 *   field_ui_base_route = "tournament_participant.participant_type"
 * )
 */
class Participant extends ContentEntityBase implements ParticipantInterface {

  use EntityChangedTrait;

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {

    /** @var QueryFactory $queryFactory */
    $queryFactory = \Drupal::getContainer()->get('entity.query');

    $tournament = $this->getTournament();

    if(Tournament::STATUS_UNSTARTED != $tournament->getStatus()){
      throw new TournamentException("Cannot add participants after the 
      tournament has started", TournamentException::DISALLOWED_ADDING_PARTICIPANTS);
    }

    $participantType = $this->getParticipantType();
    $participantEntity = $this->getReferencedEntity();

    $query = $queryFactory->get('tournament_participant')
      ->condition('tournament_reference', $tournament->id())
      ->condition($participantType.'_reference', $participantEntity->id());

    if(!empty($query->execute())){
      throw new TournamentException("It is not allowed to add the same participant
      entity twice", TournamentException::DISALLOWED_DUPLICATE_PARTICIPANT);
    }
    parent::preSave($storage);
  }

  /**
   * {@inheritdoc}
   */
  public function getTournament() {
    $entity = $this->get('tournament_reference')->entity;
    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getParticipantType() {
    return $this->bundle();
  }

  /**
   * {@inheritdoc}
   */
  public function getReferencedEntity() {
    $participantType = $this->getParticipantType();
    $entity = $this->get($participantType.'_reference')->entity;
    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the Participant entity.'))
      ->setReadOnly(TRUE);

    $fields['type'] = BaseFieldDefinition::create('string')
      ->setDescription(t('The Bundle of the Participant entity.'))
      ->setLabel(t('Type'))
      ->setReadOnly(TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Participant entity.'))
      ->setReadOnly(TRUE);

    $fields['tournament_reference'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Tournament reference'))
      ->setDescription(t('Reference to a tournament'))
      ->setSettings([
        'target_type' => 'tournament',
      ])
      ->setRequired(TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Participant entity.'))
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'hidden',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', TRUE);


    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('The language code for the Participant entity.'))
      ->setDisplayOptions('form', [
        'type' => 'language_select',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['points'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Points'))
      ->setDescription(t('@todo description'))
      ->setRevisionable(FALSE);

    $fields['win'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Win'))
      ->setDescription(t('The number of times the participant has won.'))
      ->setRevisionable(FALSE);

    $fields['draw'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Draw'))
      ->setDescription(t('The number of times the participant has drawn.'))
      ->setRevisionable(FALSE);

    $fields['loss'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Loss'))
      ->setDescription(t('The number of times the participant has list.'))
      ->setRevisionable(FALSE);

    $fields['score_for'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Score For'))
      ->setDescription(t('The total score in favor of the participant,
       e.g. Goals For in soccer.'))
      ->setRevisionable(FALSE);

    $fields['score_against'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Score Against'))
      ->setDescription(t('The total score against the participant, e.g.
       Goals Against in soccer.'))
      ->setRevisionable(FALSE);

    /** @todo Add Streak BaseFieldDefinition. */

    return $fields;
  }

}
