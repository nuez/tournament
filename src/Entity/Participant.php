<?php

namespace Drupal\tournament\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;

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

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += array(

    );
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return $this->bundle();
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * @return TournamentInterface
   */
  public function getTournament(){
    return $this->get('tournament_id')->value;
  }

  /**
   * @param $tournament_id
   * @return $this
   */
  public function setTournament($tournament_id){
    $this->set('tournament_id', $tournament_id);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? NODE_PUBLISHED : NODE_NOT_PUBLISHED);
    return $this;
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

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Participant entity.'))
      ->setSettings(array(
        'max_length' => 50,
        'text_processing' => 0,
      ))
      ->setDefaultValue('')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'hidden',
        'weight' => -4,
      ))
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Participant is published.'))
      ->setDefaultValue(TRUE);

    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('The language code for the Participant entity.'))
      ->setDisplayOptions('form', array(
        'type' => 'language_select',
        'weight' => 10,
      ))
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
