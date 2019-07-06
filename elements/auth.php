<?php
  require_once('auth/login_form.php');
  require_once('auth/register_form.php');
  require_once('auth/logout_button.php');
?>
<script type="text/javascript">
  function toggle_auth_buttons() {
    buttons = ['#logout-button', '#login-button', '#register-button'];
    for(let button of buttons) {
      for(let btn of document.querySelectorAll(button)) {
        btn.classList.toggle('d-none');
      }
    }
    if(document.title == 'Home') {
      document.querySelector('#dashboard-button').classList.toggle('d-none');
    }
  }
  function closeAllModals() {
    // Get modal close buttons.
    let close_buttons = document.querySelectorAll('.close');
    for(let cb of close_buttons) {
      cb.click();
    }
  }

  function check_login() {
    let logged_in = new Promise(function(resolve, reject) {
      let xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          resolve(this.responseText == 'YES');
        }
      };
      xhttp.open("POST", "<?php echo $ROOT_PATH . '/check_auth.php';?>", true);
      xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhttp.send('check_type=login');
    });
    return logged_in;
  }

  function get_user_id() {
    let user_id = new Promise(function(resolve, reject) {
      let xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          resolve(this.responseText);
        }
      };
      xhttp.open("POST", "<?php echo $ROOT_PATH . '/check_auth.php';?>", true);
      xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhttp.send('check_type=user_id');
    });
    return user_id;
  }
</script>
