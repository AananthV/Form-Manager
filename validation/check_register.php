<?php
  session_start();

  require '../helpers/data_validation.php';
  require '../helpers/validation.php';
  require '../helpers/database.php';
  require '../helpers/json.php';

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
