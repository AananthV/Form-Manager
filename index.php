<?php
  session_start();
  $currentPage = 'Home';
  $ROOT_PATH = '.';
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <?php require_once('elements/header.php'); ?>
  </head>
  <body>
    <?php require_once('elements/navbar.php'); ?>

    <?php require_once('elements/auth.php'); ?>
    <?php require_once('elements/footer.php'); ?>
  </body>
</html>
