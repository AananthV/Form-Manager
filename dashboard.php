<?php
  session_start();
  $currentPage = 'Dashboard';
  $ROOT_PATH = '.';
  require_once('helpers/database.php');
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
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <?php require_once('elements/header.php'); ?>
  </head>
  <body>
    <?php require_once('elements/navbar.php'); ?>

    <div class="container">
      <h1>Forms</h1>
      <ul id="form-list" class="list-group">
        <?php
          $user_forms = getValues('forms', array('*'), array('owner' => $user_id));
          foreach ($user_forms as $form) {
            $date_created = date_create($form['created']);
            echo '<li class="list-group-item d-flex flex-column flex-md-row justify-content-between align-items-center">'
                .'  <div class="col-sm-12 col-md-8 p-0 d-flex flex-column flex-md-row justify-content-between align-items-center">'
                .'    <span class="mylist-item-title text-center text-md-left text-truncate col-12 col-md-3 col-lg-4">'. $form['title'] . '</span>'
                .'    <span class="mylist-item text-center text-md-left text-truncate col-12 col-md-4 col-xl-5">' . $form['description'] . '</span>'
                .'    <span class="col-12 col-md-5 col-lg-4 col-xl-3 my-2 my-md-0 d-flex flex-row justify-content-around justify-content-md-between">'
                .'      <span class="mylist-item d-flex flex-column align-items-center justify-content-center">'
                .'        <span>Created</span>'
                .'        <span>' . $date_created->format('d-m-y') . '</span>'
                .'      </span>'
                .'      <span class="mylist-item d-flex flex-column align-items-center justify-content-center">'
                .'        <span>Responses</span>'
                .'        <span>' . $form['answers'] . '</span>'
                .'      </span>'
                .'    </span>'
                .'  </div>'
                .'  <div class="col-sm-12 col-md-4 d-flex flex-row justify-content-around">'
                .'    <a class="btn btn-outline-secondary col-6" href="'. DOMAIN . 'view_responses.php?id=' . $form['id'] . '">See Responses</a>';
            if($form['active']){
              echo '    <button type="button" class="btn btn-outline-secondary col-6" onclick=share_form(' . $form['id'] . ')>Share</button>';
            } else {
              echo '    <button type="button" class="btn btn-outline-secondary col-6" disabled>Expired</button>';
            }
            echo '  </div>'
                .'</li>';
          }
        ?>
        <li class="list-group-item">
          <button type="button" class="btn btn-success" onclick="new_form(9)">New Form</button>
        </li>
      </ul>
    </div>

    <script type="text/javascript">
      let new_form = function(template) {
        let form = document.createElement('form');
        form.method = 'post';
        form.action = '<?php echo $ROOT_PATH; ?>/form_builder.php';
        document.body.appendChild(form);
        form.submit();
      }
    </script>

    <?php require_once('elements/share_form.php'); ?>
    <?php require_once('elements/auth.php'); ?>
    <?php require_once('elements/footer.php'); ?>

    <script type="text/javascript">
      <?php
        if(
          isset($_POST['submit_form']) &&
          checkIfRowExists('forms', array('id' => $_POST['submit_form'], 'owner' => $user_id))
        ) {
          echo 'share_form(' . $_POST['submit_form'] . ');';
        }
      ?>
    </script>
  </body>
</html>
