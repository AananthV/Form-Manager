<?php
  session_start();

  $ROOT_PATH = '.';

  require_once('helpers/data_validation.php');
  require_once('helpers/validation.php');
  require_once('helpers/database.php');
  require_once('helpers/user.php');
  require_once('helpers/json.php');

  function getLogin() {
    if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
      return "ERROR: LOGGED IN";
    }

    if(
      !isset($_POST['login']) ||
      !is_json($_POST['login'])
    ) return "ERROR";

    $login = json_decode($_POST['login']);

    if(!validate_login($login)) return "ERROR";

    if(checkPassword($login->username, $login->password)){
      $_SESSION['logged_in'] = true;
      $_SESSION['username'] = $login->username;
      return "SUCCESS";
    } else {
      return "ERROR";
    }
  }

  echo getLogin();
?>
