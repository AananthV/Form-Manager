<?php
  session_start();
  $currentPage = 'Form Builder';
  $ROOT_PATH = '.';
  require_once($ROOT_PATH . '/helpers/database.php');
  require_once($ROOT_PATH . '/helpers/json.php');
  require_once($ROOT_PATH . '/helpers/data_validation.php');
  require_once($ROOT_PATH . '/helpers/validation.php');
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <?php require_once('elements/header.php'); ?>

    <link rel="stylesheet" href="./css/form_builder.css">
  </head>
  <body>
    <?php require_once('elements/navbar.php'); ?>

    <div class="container">
      <div class="row">
        <div class="form col-md-11" id="form-wrapper">
          <form>
            <div id="header-container" class="item">
            </div>
            <div id="item-container">
            </div>
            <div id="footer-container" class="d-flex justify-content-center">
              <button type="button" class="btn btn-outline-success" id="submit-button" onclick="submitEditForm()">Submit</button>
            </div>
          </form>
        </div>
        <div class="col-md-1" id="item-menu-container">
          <div id="add-item-menu">
            <button type="button" class="btn" title="Short Answer" onclick="addItem('Short Answer')"><i class="fas fa-font fa-fw"></i></button>
            <button type="button" class="btn" title="Paragraph" onclick="addItem('Paragraph')"><i class="fas fa-align-left fa-fw"></i></button>
            <button type="button" class="btn" title="Multiple Choice" onclick="addItem('Multiple Choice')"><i class="fas fa-dot-circle fa-fw"></i></button>
            <button type="button" class="btn" title="Checkboxes" onclick="addItem('Checkboxes')"><i class="far fa-check-square fa-fw"></i></button>
            <button type="button" class="btn" title="Dropdown" onclick="addItem('Dropdown')"><i class="fas fa-chevron-circle-down fa-fw"></i></button>
          </div>
        </div>
      </div>
    </div>̥

    <?php require_once($ROOT_PATH . '/form_builder/include.php');?>

    <script type="text/javascript">
      let submitEditForm = async function() {
        let logged_in = await check_login();
        if(logged_in == false) {
          document.querySelector('#login-button').click();
        } else {
          let  user_id = await get_user_id();
          if(user_id != false) {
            form.owner = user_id;
            let submit_form = new Promise(function(resolve, reject) {
              let xhttp = new XMLHttpRequest();
              xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                  resolve(this.responseText);
                }
              };
              xhttp.open("POST", "<?php echo $ROOT_PATH . '/submit_form.php';?>", true);
              xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
              xhttp.send('form_data=' + JSON.stringify(form.getFormData()));
            });
            let result = await submit_form;
            if(result.slice(0, 7) == 'SUCCESS') {
              let form = document.createElement('form');
              form.method = 'post';
              form.action = '<?php echo $ROOT_PATH; ?>/dashboard.php';

              let id_input = document.createElement('input');
              id_input.type = 'hidden';
              id_input.name = 'submit_form';
              id_input.value = result.substr(7);
              form.appendChild(id_input);

              document.body.appendChild(form);
              form.submit();
            } else {
              console.log(result);
              alert('Something went wrong...');
            }
          } else {
            alert('Something went wrong...');
          }
        }
      }

      let mainContainer = document.querySelector('#form-wrapper');

      let form = new Form(mainContainer);
      <?php
        if(
          isset($_POST['template']) &&
          is_string($_POST['template']) &&
          is_json($_POST['template']) &&
          validate_form(json_decode($_POST['template']))
        ) {
          echo 'form.constructForm(JSON.parse(' . $_POST['template'] . '));';
        } else {
          echo 'addItem();';
        }
      ?>
      form.drawEditForm();
    </script>


    <?php require_once('elements/auth.php'); ?>
    <?php require_once('elements/footer.php'); ?>
  </body>
</html>
