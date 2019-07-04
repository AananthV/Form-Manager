<!-- Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="ModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ModalCenterTitle">Log In</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p id="login-message" class="d-none"></p>
        <form>
          <div class="form-group">
            <label for="loginUsername">Username</label>
            <input type="text" class="form-control" id="loginUsername" placeholder="Username" autocomplete="username" required>
          </div>
          <div class="form-group">
            <label for="loginPassword">Password</label>
            <input type="password" class="form-control" id="loginPassword" placeholder="Password" autocomplete="current-password" required>
          </div>
          <button type="button" class="btn btn-primary" onclick="login()">Log In</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  function show_login_message() {
    document.querySelector('#login-message').classList.remove('d-none');
  }
  function login() {
    let login_data = {
      'username': document.querySelector('#loginUsername').value,
      'password': document.querySelector('#loginPassword').value
    }
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        switch(this.responseText) {
          case 'SUCCESS':
            document.querySelector('#login-message').innerHTML = 'Login Successful';
            toggle_auth_buttons();
            closeAllModals();
            break;
          case 'ERROR: LOGGED IN':
            document.querySelector('#login-message').innerHTML = 'Already Logged In!';
            show_login_message();
            break;
          default:
            document.querySelector('#login-message').innerHTML = 'Invalid Credentials';
            show_login_message();
            break;
        }
      }
    };
    xhttp.open("POST", "<?php echo $ROOT_PATH . '/login.php';?>", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send('login=' + JSON.stringify(login_data));
  }
</script>
