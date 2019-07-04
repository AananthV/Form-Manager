<script type="text/javascript">
      function logout() {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            switch(this.responseText) {
              case 'SUCCESS':
                toggle_auth_buttons();
                break;
              case 'ERROR: NOT LOGGED IN':
                alert('You are not logged in.');
                break;
            }
          }
        };
        xhttp.open("GET", "<?php echo $ROOT_PATH . '/logout.php';?>", true);
        xhttp.send();
      }
</script>
