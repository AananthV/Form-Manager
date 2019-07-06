<?php
  // Text: minlength, maxlength, contains, !contains, email, url.
  // Number: isNumber, Whole Number, > < >= <= != between !between.
  function isBetween($value, $left, $right) {
    return $value > $left && $value < $right;
  }
  function isBetweenEquals($value, $left, $right) {
    return $value >= $left && $value <= $right;
  }
  function isInteger($number) {
    return $number == intval($number);
  }
  function validateNumber($number, $validation) {
    switch ($validation->subtype) {
      case 'isNumber':
        return true;

      case 'integer':
        return isInteger($number);

      case 'whole_number':
        return isInteger($number) && $number > 0;

      case 'equals':
        return $number == $validation->equals;

      case 'not_equals':
        return $number != $validation->equals;

      case 'greater_than':
        return $number > $validation->left;

      case 'greater_than_equal_to':
        return $number >= $validation->left;

      case 'less_than':
        return $number < $validation->right;

      case 'less_than_equal_to':
        return $number < $validation->right;

      case 'between':
        return isBetween($number, $validation->left, $validation->right);

      case 'between_equals':
        return isBetweenEquals($number, $validation->left, $validation->right);

      case 'not_between':
        return !isBetween($number, $validation->left, $validation->right);

      case 'not_between_equals':
        return !isBetweenEquals($number, $validation->left, $validation->right);

      default:
        return false;
    }
  }
  function validateString($string, $validation) {
    switch($validation->subtype) {
      case 'min_length':
        return strlen($string) >= $validation->left;

      case 'max_length':
        return strlen($string) <= $validation->right;

      case 'email':
        return filter_var($string, FILTER_VALIDATE_EMAIL);

      case 'url':
        return filter_var($string, FILTER_VALIDATE_URL);

      case 'name': // Only Letters and White Space.
        return preg_match("/^[a-zA-Z ]*$/",$string);

      case 'username':
        return preg_match("/^[a-z0-9_-]{6,32}$/", $string);

      case 'password':
      return (
        preg_match('@[A-Z]@', $string) &&
        preg_match('@[a-z]@', $string) &&
        preg_match('@[0-9]@', $string) &&
        preg_match('@[^\w]@', $string) &&
        strlen($string) >= 8
      );

      default:
        return false;
    }
  }
  function validate($string, $validation) {
    if($validation->type == 'number') {
      if(is_numeric($string)) {
        return validateNumber($string + 0, $validation);
      } else {
        return false;
      }
    } else if ($validation->type == 'string') {
      return validateString($string, $validation);
    }
    return false;
  }
  function validate_name($name) {
    if(
      !is_string($name) ||
      strlen($name) == 0
      ) return false;

    return preg_match("/^[a-zA-Z ]*$/",$name);
  }
  function validate_username($username) {
    if(!is_string($username)) return false;

    return preg_match("/^[a-z0-9_-]{6,14}$/", $username);
  }
  function validate_password($password) {
    if(!is_string($password)) return false;

    return (
      preg_match('@[A-Z]@', $password) &&
      preg_match('@[a-z]@', $password) &&
      preg_match('@[0-9]@', $password) &&
      preg_match('@[^\w]@', $password) &&
      strlen($password) >= 8
    );
  }
?>
