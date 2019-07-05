<?php
  require_once('./config.php');
  function getDBInstance() {
    $host = DB_HOST;
    $db   = DB_NAME;
    $user = DB_USERNAME;
    $pass = DB_PASSWORD;
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    try {
         $pdo = new PDO($dsn, $user, $pass, $options);
         return $pdo;
    } catch (\PDOException $e) {
         throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
  }

  function checkIfRowExists($table, $keys) {
    // Get PDO instance
    $db = getDBInstance();

    $findQuery = '';
    $params = array();
    foreach ($keys as $key => $value) {
      $findQuery .= $key . ' = :' . $key . ' AND ';
      $params[':' . $key] = $value;
    }
    $findQuery = '('. rtrim($findQuery, ' AND ') . ')';

    $sql = 'SELECT EXISTS(SELECT * FROM ' . $table . ' WHERE ' . $findQuery .') as rowExists';

    try {
      $query = $db->prepare($sql);
      $query->execute($params);
      return $query->fetch(PDO::FETCH_ASSOC)['rowExists'] == 1;
    } catch (\PDOException $e) {
         throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
  }

  function insertValues($table, $values) {
    // Get PDO instance
    $db = getDBInstance();

    $field_list = '';
    $placeholder_list = '';
    $params = array();
    foreach ($values as $key => $value) {
      $field_list .= $key . ', ';
      $placeholder_list .= ':' . $key . ', ';
      $params[':' . $key] = $value;
    }
    $field_list = '(' . rtrim($field_list, ', ') . ')';
    $placeholder_list = '(' . rtrim($placeholder_list, ', ') . ')';

    $sql = 'INSERT INTO ' . $table . ' ' . $field_list . ' VALUES ' . $placeholder_list;

    try {
      $query = $db->prepare($sql);
      $query->execute($params);
      return $db->lastInsertId();
    } catch (\PDOException $e) {
      return false;
    }
  }

  function getValues($table, $fields, $keys) {
    // Get PDO instance
    $db = getDBInstance();

    $findQuery = '';
    $params = array();
    foreach ($keys as $key => $value) {
      $findQuery .= $key . ' = :' . $key . ' AND ';
      $params[':' . $key] = $value;
    }
    $findQuery = '('. rtrim($findQuery, ' AND ') . ')';

    $sql = 'SELECT ' . implode(',', $fields) . ' FROM ' . $table . ' WHERE ' . $findQuery;

    try {
      $query = $db->prepare($sql);
      $query->execute($params);
      return $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
         throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
  }
?>
