<?php

namespace Drupal\tournament\Exception;

/**
 * An exception class to be thrown for different Tournament related errors.
 */
class TournamentException extends \Exception {
  const DISALLOWED_PARTICIPANT_TYPE = 100;
  const DISALLOWED_CHANGE_PARTICIPANT_TYPE = 101;
  const DISALLOWED_ADDING_PARTICIPANTS = 102;
  const DISALLOWED_DUPLICATE_PARTICIPANT = 103;
}
