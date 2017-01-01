<?php

/**
 * @file
 * Contains \Drupal\tournament\Entity\Tournament.
 */

namespace Drupal\tournament\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\FieldItemList;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\tour\Entity\Tour;
use Drupal\tournament\Exception\TournamentException;
use Drupal\user\UserInterface;

/**
 * Defines the Tournament entity.
 *
 * @ingroup tournament
 *
 * @ContentEntityType(
 *   id = "tournament",
 *   label = @Translation("Tournament"),
 *   bundle_label = @Translation("Tournament type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\tournament\Entity\TournamentListBuilder",
 *     "views_data" = "Drupal\tournament\Entity\TournamentViewsData",
 *     "form" = {
 *       "default" = "Drupal\tournament\Form\TournamentForm",
 *       "add" = "Drupal\tournament\Form\TournamentForm",
 *       "edit" = "Drupal\tournament\Form\TournamentForm",
 *       "delete" = "Drupal\tournament\Form\TournamentDeleteForm",
 *       "add_participants" = "Drupal\tournament\Form\TournamentAddParticipantsForm",
 *     },
 *     "access" = "Drupal\tournament\Entity\TournamentAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\tournament\Entity\TournamentHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "tournament",
 *   admin_permission = "administer tournament entities",
 *   translatable = TRUE,
 *   entity_keys = {
 *     "id" = "id",
 *     "bundle" = "type",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *     "participant_type" = "participant_type"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/tournament/tournament/{tournament}",
 *     "edit-form" = "/admin/structure/tournament/tournament/{tournament}/edit",
 *     "delete-form" = "/admin/structure/tournament/tournament/{tournament}/delete",
 *     "add-participants-form" = "/admin/structure/tournament/tournament/{tournament}/add-participants",
 *     "collection" = "/admin/structure/tournament/tournament",
 *   },
 *   field_ui_base_route = "tournament.tournament_type"
 * )
 */
class Tournament extends ContentEntityBase implements TournamentInterface {

  use EntityChangedTrait;

  use StringTranslationTrait;

  const STATUS_UNSTARTED = 0;

  const STATUS_STARTED = 1;

  const STATUS_FINISHED = 2;

  /**
   * Array of participants that belong to the tournament.
   *
   * @var Participant[]|bool
   */
  public $participants = FALSE;

  /**
   * Array of matches that belong to the tournament.
   *
   * @var Match[]|bool
   */
  public $matches = FALSE;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);

    $values += array(
      'user_id' => \Drupal::currentUser()->id(),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {

    // Don't allow participant types other than the ones defined
    // as bundles to the participant entity.
    $allowedBundles = \Drupal::getContainer()
      ->get('entity_type.bundle.info')
      ->getBundleInfo('tournament_participant');
    $tournamentManager = \Drupal::getContainer()
      ->get('plugin.manager.tournament.manager');
    if (!in_array($this->getParticipantType(), array_keys($allowedBundles))) {
      throw new TournamentException("Not allowed Participant Bundle", TournamentException::DISALLOWED_PARTICIPANT_TYPE);
    }

    // Don't allow changing participant types after participants have
    // been added.
    /** @var Tournament $original */
    $original = $this->original;
    if ($original) {
      $participants = $tournamentManager->getParticipants($this);
      $participantType = $this->getParticipantType();
      $participantTypeOriginal = $original->getParticipantType();
      if (!empty($participants) && $participantType != $participantTypeOriginal) {
        throw new TournamentException("Not allowed to change participant type
        after adding participants", TournamentException::DISALLOWED_CHANGE_PARTICIPANT_TYPE);
      }
    }

    parent::preSave($storage);
  }

  /**
   * {@inheritdoc}
   */
  public function getAuthor() {
    return $this->get('user_id')->getEntity();
  }

  public function getStatus() {
    return $this->getEntityKey('status');
  }

  /**
   * @todo Documentation.
   */
  public function getTournamentType() {
    return $this->getEntityKey('type');
  }

  /**
   * Get participant type.
   *
   * @return string Participant Type
   */
  public function getParticipantType() {
    return $this->getEntityKey('participant_type');
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the Tournament entity.'))
      ->setReadOnly(TRUE);

    $fields['type'] = BaseFieldDefinition::create('string')
      ->setDescription(t('The Bundle of the Tournament entity.'))
      ->setLabel(t('Tournament type'))
      ->setReadOnly(TRUE);

    $fields['participant_type'] = BaseFieldDefinition::create('list_string')
      ->setDescription(t('The type of participant the tournament requires.'))
      ->setLabel(t('Participant'))
      ->setSettings([
        'allowed_values' => \Drupal::getContainer()
          ->get('entity_type.bundle.info')
          ->getBundleInfo('tournament_participant'),
      ])
      ->setRequired(TRUE)
      ->setReadOnly(TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Tournament entity.'))
      ->setReadOnly(TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'))
      ->setTranslatable(FALSE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'))
      ->setTranslatable(FALSE);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Tournament entity.'))
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDefaultValueCallback('Drupal\node\Entity\Node::getCurrentUserId')
      ->setTranslatable(FALSE)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Tournament entity.'))
      ->setTranslatable(TRUE)
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
        'type' => 'string_textfield',
        'weight' => -4,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('list_integer')
      ->setLabel(t('Published status'))
      ->setDescription(t('A boolean indicating whether the Tournament is published.'))
      ->setSettings([
        'allowed_values' => [
          self::STATUS_UNSTARTED,
          self::STATUS_STARTED,
          self::STATUS_FINISHED,
        ],
      ])
      ->setDisplayOptions('form', array(
        'weight' => -4,
      ));


    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('The language code for the Tournament entity.'))
      ->setDisplayOptions('form', array(
        'type' => 'language_select',
        'weight' => 10,
      ))
      ->setDisplayConfigurable('form', TRUE);

    $fields['config'] = BaseFieldDefinition::create('map')
      ->setLabel(t('Configuration'))
      ->setDescription(t('Serialized configuration for the tournament'));


    return $fields;
  }
}
