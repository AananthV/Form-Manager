<?php
  session_start();

  require 'helpers/data_validation.php';
  require 'helpers/validation.php';
  require 'helpers/database.php';
  require 'helpers/user.php';
  require 'helpers/json.php';

  function getRegister() {
    if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
      return "ERROR: LOGGED IN";
    }

    if(
      !isset($_POST['register']) ||
      !is_json($_POST['register'])
    ) return "ERROR";

    $register = json_decode($_POST['register']);

    $validation = validate_register($register);

    if(
      !$validation->first_name ||
      !$validation->last_name ||
      !$validation->username ||
      !$validation->password
      ) return "ERROR";

    if(addUser(
      $register->username,
      $register->password,
      $register->first_name,
      $register->last_name
      ) != false
    ) {
      $_SESSION['logged_in'] = true;
      $_SESSION['username'] = $register->username;
      return "SUCCESS";
    } else {
      return "ERROR";
    }
  }

  echo getRegister();
?>
