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
    require_once('go_home.php');
  }

  $form_id = $_GET['id'];
  $form = (object) getForm($form_id);
  $answer_ids = getValues(
    'answers',
    array('id'),
    array('form' => $form_id)
  );

  function getChoice($choices, $id) {
    foreach ($choices as $choice) {
      if($choice['id'] == $id) {
        if($choice['isOther'] == true) return false;
        return $choice['value'];
      }
    }
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
      <h1>Responses</h1>
      <div id="response-list">
        <?php
          foreach ($answer_ids as $aids) {
            $answer_id = $aids['id'];
            $answer = getAnswer($form_id, $answer_id);

            echo '<div class="card" onclick="toggle_card_body(this)">'
                .'  <div class="card-header d-flex justify-content-between">'
                .'    <span>' . $answer['username'] . '</span>'
                .'    <span>' . $answer['answered']->format('d-m-y') . '</span>'
                .'  </div>'
                .'  <div class="card-body d-none">';

            $question_number = 1;
            foreach ($form->items as $item) {
              echo '<h5 class="card-title">'. $question_number++ . '. ' . $item->question . '</h5>';
              $answer_text = '';
              if(is_string($answer[$item->id])) {
                $answer_text = $answer[$item->id];
              } else if (is_object($answer[$item->id])) {
                $answer_chosen = array();
                foreach ($answer[$item->id]->selectedIds as $choice_id) {
                  $choice = getChoice($item->choices, $choice_id);
                  if($choice !== false) {
                    $answer_chosen[] = $choice;
                  }
                }
                if($answer[$item->id]->otherSelected) {
                  $answer_chosen[] = $answer[$item->id]->otherAnswer;
                }
                $answer_text = implode(', ', $answer_chosen);
              }
              if($answer_text == '') {
                $answer_text = 'Not Answered.';
              }
              echo '<p class="card-text">' . $answer_text . '</p>';
            }
            echo '</div></div>';
          }
        ?>
      </div>
    </div>

    <script type="text/javascript">
      let toggle_card_body = function(e) {
        e.querySelector('.card-body').classList.toggle('d-none');
      }
    </script>

    <?php require_once('elements/auth.php'); ?>
    <?php require_once('elements/footer.php'); ?>
  </body>
</html>
