<?php
  session_start();

  $ROOT_PATH = '.';

  require_once('helpers/json.php');
  require_once('helpers/data_validation.php');
  require_once('helpers/validation.php');
  require_once('helpers/database.php');
  require_once('helpers/user.php');
  require_once('helpers/form.php');

  function submit_answer() {
    if(isset($_SESSION['submit']) && $_SESSION['submit'] == true) {
      return 'ERROR: SUBMISSION IN PROGRESS';
    }

    $_SESSION['submit'] = true;

    if(!(
      isset($_SESSION['logged_in']) &&
      $_SESSION['logged_in'] == true  &&
      isset($_SESSION['username']) &&
      is_string($_SESSION['username'])
    )) {
      $_SESSION['submit'] = false;
      return 'ERROR: NOT LOGGED IN';
    }

    if(
      !isset($_POST['answer_data']) ||
      !is_string($_POST['answer_data']) ||
      !is_json($_POST['answer_data'])
    ) {
      $_SESSION['submit'] = false;
      return 'ERROR: INVALID ANSWER DATA';
    }

    $answer = json_decode($_POST['answer_data']);

    if(!property_exists($answer, 'user') || $answer->user != getUserId($_SESSION['username'])) {
      $_SESSION['submit'] = false;
      return 'ERROR: INCORRECT USER';
    }

    $validation = validate_answer($answer);

    if($validation === true) {
      // Begin transaction
      $db = getDbInstance();
      $db->beginTransaction();

      $added = addAnswer($answer);
      $_SESSION['submit'] = false;
      if($added !== false) {
        // Commit
        $db->commit();

        return 'SUCCESS';
      } else {
        // Rollback
        $db->rollback();

        return 'ERROR: ADD ANSWER FAILED.';
      }
    } else {
      $_SESSION['submit'] = false;
      return $validation;
    }
  }

  echo submit_answer();
?>
