<?php
  session_start();
  $currentPage = 'Answer';
  $ROOT_PATH = '.';

  require_once('helpers/database.php');
  require_once('helpers/form.php');
  require_once('helpers/user.php');
  require_once('config.php');

  if(!(
    isset($_GET['id']) &&
    checkIfRowExists('forms', array('id' => $_GET['id']))
  )) {
    require_once('go_home.php');
  }

  $form = getForm($_GET['id']);
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
      <div class="item" id="form-answer-wrapper">
        <form>
          <div id="header-container">
          </div>
          <div id="item-container">
          </div>
          <div id="footer-container" class="d-flex justify-content-center">
            <button type="button" class="mt-3 btn btn-outline-success" id="submit-button" onclick="submitForm()">Submit</button>
          </div>
        </form>
      </div>
    </div>

    <?php require_once('form_builder/include.php'); ?>

    <script type="text/javascript">
      let invalid_answers = [];

      let hide_invalid_answers = function() {
        for(let answer_id of invalid_answers) {
          let item = form.findItem(answer_id);
          if(item.type < 2 || item.type == 4) {
            let answer_input = form.itemContainer.querySelector('#answer-' + answer_id);
            answer_input.classList.remove('border', 'border-danger');
          } else {
            let answer_body = form.itemContainer.querySelector('#answer-' + answer_id +'-container');
            answer_body.classList.remove('border', 'border-danger');
            if(item.hasOther) {
              let other_input = answer_body.querySelector('#answer-' + answer_id + '-other');
              other_input.classList.remove('border', 'border-danger');
            }
          }
        }
      }

      let show_invalid_answers = function() {
        for(let answer_id of invalid_answers) {
          let item = form.findItem(answer_id);
          if(item.type < 2 || item.type == 4) {
            let answer_input = form.itemContainer.querySelector('#answer-' + answer_id);
            answer_input.classList.add('border', 'border-danger');
          } else {
            if(item.getAnswer().otherSelected) {
              let other_input = form.itemContainer.querySelector('#answer-' + answer_id + '-other');
              other_input.classList.add('border', 'border-danger');
            } else {
              let answer_body = form.itemContainer.querySelector('#answer-' + answer_id +'-container');
              answer_body.classList.add('border', 'border-danger');
            }
          }
        }
      }

      let submitForm = async function() {
        let logged_in = await check_login();
        if(logged_in == false) {
          document.querySelector('#login-button').click();
        } else {
          let user_id = await get_user_id();
          if(user_id != false) {
            form.owner = user_id;
            console.log(JSON.stringify(form.getAnswer()));
            let submit_answer = new Promise(function(resolve, reject) {
              let xhttp = new XMLHttpRequest();
              xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                  resolve(this.responseText);
                }
              };
              xhttp.open("POST", "<?php echo $ROOT_PATH . '/submit_answer.php';?>", true);
              xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
              xhttp.send('answer_data=' + JSON.stringify(form.getAnswer()));
            });
            let result = await submit_answer;
            if(result.slice(0, 7) == 'SUCCESS') {
              window.location = '<?php echo DOMAIN; ?>answer_success.php';
            } else if (result.slice(0, 5) == 'ERROR') {
              if(result == 'ERROR: ALREADY SUBMITTED') {
                alert('You have already submitted this form.');
              } else {
                console.log(result);
                alert('Something went wrong...');
              }
            } else {
              try {
                hide_invalid_answers();
                invalid_answers = JSON.parse(result);
                show_invalid_answers();
              } catch (e) {
                console.log(result);
              }
            }
          } else {
            alert('Something went wrong...');
          }
        }
      }

      let mainContainer = document.querySelector('#form-answer-wrapper');

      let form = new Form(mainContainer);

      form.constructForm(JSON.parse('<?php echo json_encode($form); ?>'));

      form.drawForm();
    </script>

    <?php require_once('elements/auth.php'); ?>
    <?php require_once('elements/footer.php'); ?>
  </body>
</html>
