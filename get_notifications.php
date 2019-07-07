<?php
  session_start();
  $ROOT_PATH = '.';

  require_once('config.php');
  require_once('helpers/database.php');

  function getNotification() {
    if(!(
      isset($_SESSION['logged_in']) &&
      $_SESSION['logged_in'] == true  &&
      isset($_SESSION['username']) &&
      is_string($_SESSION['username'])
    )) {
      return 'ERROR: NOT LOGGED IN';
    }

    $user_id = getValues('users', array('id'), array('username' => $_SESSION['username']))[0]['id'];
    if($user_id == false) {
      return 'ERROR: NOT PROPERLY LOGGED IN';
    }

    $lastNotification = getValues('notifications', array('*'), array('user' => $user_id), array('id' => 'DESC'), array(0, 1));

    if(count($lastNotification) == 0) {
      return 'NO NOTIFICATIONS';
    }

    $lastNotification = $lastNotification[0];

    if(!isset($_SESSION['last_notification'])) {
      $_SESSION['last_notification'] = $lastNotification['id'];
      return 'NO LAST NOTIFICATION';
    }

    if($_SESSION['last_notification'] == $lastNotification['id']) {
      return 'CAUGHT UP';
    }

    $_SESSION['last_notification'] = $lastNotification['id'];
    return 'SUCCESS: ' . json_encode($lastNotification);
  }

  echo getNotification();
?>
