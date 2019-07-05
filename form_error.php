<?php
  session_start();
  $currentPage = 'Ohno!';
  $ROOT_PATH = '.';
  require_once('config.php');

  $error_codes = array(
    '1' => 'That form does not exist.',
    '2' => 'That form is no longer active.',
    '3' => 'You have already answered this form.',
    '4' => 'Something went wrong...';
  );

  $error_code = 1;

  if(
    isset($_GET['error_code']) &&
    is_numeric($_GET['error_code']) &&
    isset($error_codes[$_GET['error_code']])
  ) {
    $error_code = $_GET['error_code'];
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
      <div class="jumbotron text-center">
        <h1 class="display-4">Ohno!</h1>
        <p class="lead"><?php echo $error_codes[$error_code]; ?></p>
        <hr class="my-4">
        <a class="btn btn-primary" href="<?php echo DOMAIN; ?>" role="button">Home</a>
        <a class="btn btn-primary" href="<?php echo DOMAIN; ?>dashboard.php" role="button">Dashboard</a>
      </div>
    </div>

    <?php require_once('elements/auth.php'); ?>
    <?php require_once('elements/footer.php'); ?>
  </body>
</html>
