<?php
  require_once($ROOT_PATH . '/config.php');
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

  function getValues($table, $fields, $keys = null, $orders = null, $limit = null) {
    // Get PDO instance
    $db = getDBInstance();

    $params = array();

    $sql = 'SELECT ' . implode(',', $fields) . ' FROM ' . $table;

    if(
      !is_null($keys) &&
      is_array($keys) &&
      count($keys) > 0
    ) {
      $findQuery = '';
      foreach ($keys as $key => $value) {
        if(
          is_array($value) &&
          isset($value['type']) &&
          isset($value['value']) &&
          $value['type'] == 'LIKE'
        ) {
          $findQuery .= $key . ' LIKE :' . $key . ' AND ';
          $params[':' . $key] = $value['value'];
        } else {
          $findQuery .= $key . ' = :' . $key . ' AND ';
          $params[':' . $key] = $value;
        }
      }
      $findQuery = '('. rtrim($findQuery, ' AND ') . ')';
      $sql .= ' WHERE ' . $findQuery;
    }

    if(
      !is_null($orders) &&
      is_array($orders) &&
      count($orders) > 0
    ) {
      $orderQuery = '';
      foreach ($orders as $key => $value) {
        $orderQuery .= $key . ' ' . $value . ', ';
      }
      $orderQuery = rtrim($orderQuery, ', ');
      $sql .= ' ORDER BY ' . $orderQuery;
    }

    if(
      !is_null($limit) &&
      isset($limit['offset']) &&
      isset($limit['count'])
    ) {
      $sql .= ' LIMIT ' . $limit['offset'] . ', ' . $limit['count'];
    }

    try {
      $query = $db->prepare($sql);
      $query->execute($params);
      return $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
         throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
  }
?>
