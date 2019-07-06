<nav class="navbar sticky-top navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="<?php echo DOMAIN; ?>">Form Builder</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <?php
        $nav_links = array(
          'Home' => './index.php',
          'Dashboard' => './dashboard.php'
        );
        foreach ($nav_links as $name => $url) {
          echo '<li class="nav-item'. (($currentPage == $name) ? ' active' : '') . '">'
              .'<a class="nav-link" href="'. $url .'">' . $name . '</a></li>';
        }
      ?>
    </ul>
    <ul class="navbar-nav">
      <?php
        $logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true;
          echo '<button type="button" id="logout-button" class="btn btn-outline-danger' . ($logged_in ? '' : ' d-none') . '" onclick="logout()">Log Out</button>'
              .'<button type="button" id="login-button" class="btn' . ($logged_in ? ' d-none' : '') .'" data-toggle="modal" data-target="#loginModal">Log In</button>'
              .'<button type="button" id="register-button" class="btn' . ($logged_in ? ' d-none' : '') .'" data-toggle="modal" data-target="#registerModal">Register</button>';
      ?>
    </ul>
  </div>
</nav>
