<?php
  session_start();

  $ROOT_PATH = '.';

  require_once('helpers/json.php');
  require_once('helpers/data_validation.php');
  require_once('helpers/validation.php');
  require_once('helpers/database.php');
  require_once('helpers/user.php');
  require_once('helpers/form.php');

  function submit_form() {
    // Return if submission is in progress.
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
      !isset($_POST['form_data']) ||
      !is_string($_POST['form_data']) ||
      !is_json($_POST['form_data'])
    ) {
      $_SESSION['submit'] = false;
      return 'ERROR: INVALID FORM DATA';
    }

    $form = json_decode($_POST['form_data']);

    if(!property_exists($form, 'owner') || $form->owner != getUserId($_SESSION['username'])) {
      $_SESSION['submit'] = false;
      return 'ERROR: INCORRECT OWNER';
    }

    $validation = validate_form($form);

    if($validation === true) {
      $added = addForm($form);
      $_SESSION['submit'] = false;
      if($added !== false) {
        return 'SUCCESS' . $added;
      } else {
        return 'ERROR: ADD FORM FAILED: ' . $added;
      }
    } else {
      $_SESSION['submit'] = false;
      return $validation;
    }
  }

  echo submit_form();
?>
