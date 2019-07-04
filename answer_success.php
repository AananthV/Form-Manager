<?php
  session_start();
  $currentPage = 'Submitted!';
  $ROOT_PATH = '.';
  require_once('config.php');
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <?php require 'elements/header.php' ?>
  </head>
  <body>
    <?php require 'elements/navbar.php' ?>

    <div class="container">
      <div class="jumbotron">
        <h1 class="display-4">Answer Submitted!</h1>
        <p class="lead">Click on the buttons to go home or to dashboard!</p>
        <hr class="my-4">
        <a class="btn btn-primary" href="<?php echo DOMAIN; ?>" role="button">Home</a>
        <a class="btn btn-primary" href="<?php echo DOMAIN; ?>dashboard.php" role="button">Dashboard</a>
      </div>
    </div>

    <?php require 'elements/auth.php' ?>
    <?php require 'elements/footer.php' ?>
  </body>
</html>
