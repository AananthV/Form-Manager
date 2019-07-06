<?php
  session_start();
  $currentPage = 'Responses';
  $ROOT_PATH = '.';
  require_once('helpers/database.php');
  require_once('helpers/form.php');
  require_once('helpers/user.php');
  require_once('config.php');
  if(!(
    isset($_SESSION['logged_in']) &&
    $_SESSION['logged_in'] == true  &&
    isset($_SESSION['username']) &&
    is_string($_SESSION['username'])
  )) {
    require_once('go_home.php');
  }
  $user_id = getValues('users', array('id'), array('username' => $_SESSION['username']))[0]['id'];
  if($user_id == false) {
    require_once('go_home.php');
  }
  if(
    !checkIfRowExists('forms', array('id' => $_GET['id'], 'owner' => $user_id))
  ) {
    header('Location: ' . DOMAIN . 'form_error.php?error_code=1');
  }

  $form_id = $_GET['id'];
  $form_title = getValues('forms', array('title'), array('id' => $form_id))[0]['title'];

  $individual_responses = false;

  if(
    isset($_GET['individual_responses']) &&
    $_GET['individual_responses'] == 'true'
  ) {
    $individual_responses = true;
  }
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <?php require_once('elements/header.php'); ?>
  </head>
  <body>
    <?php require_once('elements/navbar.php'); ?>

    <?php
      if($individual_responses) {
        require_once('view_responses/individual.php');
      } else {
        require_once('view_responses/group.php');
      }
    ?>

    <?php require_once('elements/auth.php'); ?>
    <?php require_once('elements/footer.php'); ?>
  </body>
</html>
