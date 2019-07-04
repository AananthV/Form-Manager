<?php
  function addUser($username, $password, $first_name, $last_name) {
    // Hash the password.
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    $values = array(
      'username' => $username,
      'password_hashed' => $password_hashed,
      'first_name' => $first_name,
      'last_name' => $last_name
    );

    return insertValues('users', $values);
  }

  function checkPassword($username, $password) {
    // Retrieve password.
    try {
      $retrievedValue = getValues(
        'users',
        array('password_hashed'),
        array('username' => $username)
      );
    } catch (\PDOException $e) {
      return false;
    }

    $password_hashed = $retrievedValue[0]['password_hashed'];

    return password_verify($password, $password_hashed);
  }

  function getUserId($username) {
    try {
      $userId = getValues('users', array('id'), array('username' => $username))[0]['id'];
    } catch (\PDOException $e) {
      return false;
    }
    return $userId;
  }
?>
