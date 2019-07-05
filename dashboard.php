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

  // Changing page.
  $page = 1;
  if(
    isset($_GET['page']) &&
    is_numeric($_GET['page']) &&
    intval($_GET['page']) > 0
  ) {
    $page = $_GET['page'];
  }

  // Sorting.
  $sort_by_options = array('title', 'description', 'created', 'answers');
  $sort_by_list = array(
    'title' => 'Title',
    'description' => 'Description',
    'created' => 'Date Created',
    'answers' => 'Responses'
  );
  $sort_order_options = array('ASC', 'DESC');
  $sort_order_list = array(
    'ASC' => 'Ascending',
    'DESC' => 'Descending'
  );
  $sort_by = 'created';
  $sort_order = 'DESC';
  if(
    isset($_GET['sort_by']) &&
    in_array($_GET['sort_by'], $sort_by_options) &&
    isset($_GET['sort_order']) &&
    in_array($_GET['sort_order'], $sort_order_options)
  ) {
    $sort_by = $_GET['sort_by'];
    $sort_order = $_GET['sort_order'];
  }

  $search_string = '';

  // Searching.
  if(
    isset($_GET['search'])
  ) {
    $search_string = $_GET['search'];
  }
  $user_forms = getValues(
    'forms',
    array('*'),
    array(
      'owner' => $user_id,
      'title' => array('type' => 'LIKE', 'value' => '%' . $search_string . '%')
    ),
    array($sort_by => $sort_order),
    array('offset' => ($page - 1) * 10, 'count' => 10)
  );
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
        <li class="list-group-item d-flex flex-column flex-lg-row-reverse">
          <div class="col-12 col-lg-5 align-items-center mb-2 mb-lg-0">
            <div class="input-group">
              <input type="search" class="form-control" placeholder="Search" aria-label="Search" id="search-string">
              <div class="input-group-append">
                <button class="btn btn-outline-success" type="button" id="search-button" onclick="search_button()">Search</button>
              </div>
            </div>
          </div>
          <div class="col-12 col-lg-7 d-flex flex-column flex-sm-row align-items-center justify-content-around">
            <span class="mylist-item-title mr-0 mr-sm-2 mb-2 mb-sm-0">Sort By<span class="d-none d-sm-inline">:</span>
            </span>
            <select class="form-control col mb-2 mb-sm-0" id="sort_by">
              <?php
                foreach ($sort_by_list as $key => $value) {
                  echo '<option value="' . $key . '"' . (($sort_by == $key) ? ' selected' : '') . '>' . $value . '</option>';
                }
              ?>
            </select>
            <select class="form-control col mb-2 mb-sm-0" id="sort_order">
              <?php
                foreach ($sort_order_list as $key => $value) {
                  echo '<option value="' . $key . '"' . (($sort_order == $key) ? ' selected' : '') . '>' . $value . '</option>';
                }
              ?>
            </select>
            <button class="btn btn-outline-success" onclick="sort_button()">Go!</button>
          </div>
        </li>
        <?php
          if(count($user_forms) == 0) {
            echo '<li class="list-group-item text-center">No forms found.</li>';
          }
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
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <?php
            if($page > 1) {
              echo '<a class="btn btn-outline-primary" href="?page=' . ($page - 1) . '&sort_by=' . $sort_by . '&sort_order=' . $sort_order . '">Prev</a>';
            }
          ?>
          <button type="button" class="btn btn-success" onclick="new_form(0)">New Form</button>
          <?php
            if(count($user_forms) == 10) {
              echo '<a class="btn btn-outline-primary" href="?page=' . ($page + 1) . '&sort_by=' . $sort_by . '&sort_order=' . $sort_order . '">Next</a>';
            }
          ?>
        </li>
      </ul>
    </div>

    <script type="text/javascript">
      let search_button = function() {
        let search_string = document.querySelector('#search-string').value;
        window.location = "<?php echo DOMAIN . 'dashboard.php?sort_by=' . $sort_by . '&sort_order=' . $sort_order . '&search=' ?>" + encodeURIComponent(search_string);
      }

      let sort_button = function() {
        let sort_by = document.querySelector('#sort_by').value;
        let sort_order = document.querySelector('#sort_order').value;
        window.location = "<?php echo DOMAIN . 'dashboard.php?search=' . urlencode($search_string); ?>&sort_by=" + sort_by + "&sort_order=" + sort_order;
      }

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
