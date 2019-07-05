<?php
    session_start();

    $ROOT_PATH = '.';

    require_once('helpers/database.php');
    require_once('helpers/user.php');

    function checkLogin() {
      if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
        return 'YES';
      } else {
        return 'NO';
      }
    }

    if(isset($_POST['check_type'])) {
      switch($_POST['check_type']) {
        case 'login':
          echo checkLogin();
          break;
        case 'user_id':
          if(checkLogin() == 'YES') {
            echo getUserId($_SESSION['username']);
          } else {
            echo false;
          }
          break;
      }
    }
?>
