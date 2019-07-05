<?php
  /*
  * The domain where the Form Builder is hosted.
  * It must end in a forward slash '/'
  * 'http://www.example.com/' is valid while 'http://www.example.com' is not.
  */
  define('DOMAIN', 'http://localhost/');

  /*
  * The MYSQL database configuration.
  */
  define('DB_HOST', 'localhost');
  define('DB_NAME', 'form_manager');
  define('DB_USERNAME', 'root');
  define('DB_PASSWORD', '');

  /*
  * Default timezone.
  * Set this to the timezone your MYSQL Databse uses.
  * Must be in IntlTimeZone format.
  */
  date_default_timezone_set("Asia/Calcutta");
?>
