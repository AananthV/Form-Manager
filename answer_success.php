<?php
  session_start();
  $currentPage = 'Submitted!';
  $ROOT_PATH = '.';
  require_once('config.php');
  if(!isset($_GET['id'])) {
    header('Location: ' . DOMAIN . 'form_error.php?error_code=1');
  }
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <?php require_once('elements/header.php'); ?>
  </head>
  <body>
    <?php require_once('elements/navbar.php'); ?>

    <div class="container">
      <div class="jumbotron">
        <h1 class="display-4">Answer Submitted!</h1>
        <p class="lead">Share the form!</p>
        <hr class="my-4">
        <a class="btn btn-primary mb-2" href="<?php echo DOMAIN; ?>" role="button">Home</a>
        <a class="btn btn-primary mb-2" href="<?php echo DOMAIN; ?>dashboard.php" role="button">Dashboard</a>
        <button type="button" class="btn btn-primary mb-2" onclick=share_form(<?php echo $_GET['id']; ?>)>Share</button>
      </div>
    </div>

    <?php require_once('elements/auth.php'); ?>
    <?php require_once('elements/share_form.php'); ?>
    <?php require_once('elements/footer.php'); ?>
  </body>
</html>
