<?php
  session_start();

  function getLogout() {
    if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
      session_destroy();
      return "SUCCESS";
    } else {
      return "ERROR: NOT LOGGED IN";
    }
  }

  echo getLogout();
?>
