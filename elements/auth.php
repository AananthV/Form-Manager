<?php
  require 'auth/login_form.php';
  require 'auth/register_form.php';
  require 'auth/logout_button.php';
?>
<script type="text/javascript">
  function toggle_auth_buttons() {
    buttons = ['#logout-button', '#login-button', '#register-button'];
    for(let button of buttons) {
      document.querySelector(button).classList.toggle('d-none');
    }
  }
  function closeAllModals() {

    // get modals
    const modals = document.getElementsByClassName('modal');

    // on every modal change state like in hidden modal
    for(let i=0; i<modals.length; i++) {
      modals[i].classList.remove('show');
      modals[i].setAttribute('aria-hidden', 'true');
      modals[i].setAttribute('style', 'display: none');
    }

     // get modal backdrops
     const modalsBackdrops = document.getElementsByClassName('modal-backdrop');

     // remove every modal backdrop
     for(let i=0; i<modalsBackdrops.length; i++) {
       document.body.removeChild(modalsBackdrops[i]);
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
