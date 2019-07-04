<?php
  session_start();

  require_once('../helpers/data_validation.php');
  require_once('../helpers/validation.php');
  require_once('../helpers/database.php');
  require_once('../helpers/json.php');

  function getCheck() {
    if(
      !isset($_POST['register']) ||
      !is_json($_POST['register'])
    ) return "ERROR";

    $register = json_decode($_POST['register']);

    $validation = validate_register($register);

    if($validation->username) {
      $validation->username = !checkIfRowExists('users', array('username' => $register->username));
    }

    return json_encode($validation);
  }

  echo getCheck();
?>
