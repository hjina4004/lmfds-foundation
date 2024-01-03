<?php

/**
 * Model - LMFDS Foundation Model class.
 *
 * Version 1.0.0
 */

namespace Lmfriends\LmfdsFoundation;

use mysqli;

class Model
{
  protected $_env;
  protected $_tableName;
  protected $_selectItems;
  protected $_join;
  protected $_orderBy;

  public function __construct($env, $tableName)
  {
    $this->_env = $env;
    $this->_tableName = $tableName;
    $this->initSql();
  }

  protected function initSql()
  {
    $this->_selectItems = "*";
    $this->_join = "";
    $this->_orderBy = "ORDER BY id DESC";
  }

  public function select($option)
  {
    $this->_selectItems = $option;
    return $this;
  }

  public function leftJoin($option)
  {
    $this->_join = "LEFT JOIN $option";
    return $this;
  }

  public function orderBy($option)
  {
    $this->_orderBy = "ORDER BY " . $option;
    return $this;
  }

  public function findAll($option = null)
  {
    $condition = "";
    $sql = "SELECT {$this->_selectItems}
      FROM {$this->_tableName}
      {$this->_join}
      $condition
      {$this->_orderBy}";
    $result = $this->queryExecute($sql);
    return $result;
  }

  public function findById($id)
  {
    $condition = "WHERE id = $id";
    $sql = "SELECT {$this->_selectItems} FROM {$this->_tableName} $condition";
    $row = $this->queryExecute($sql);
    return isset($row[0]) ? $row[0] : null;
  }

  public function updateById($id, $data)
  {
    $condition = "WHERE id = $id";

    $querySet = [];
    foreach ($data as $key => $value) array_push($querySet, "$key = '$value'");
    $querySet = implode(', ', $querySet);

    $sql = "UPDATE {$this->_tableName} SET $querySet $condition";
    return $this->queryExecute($sql);
  }

  public function daleteById($id)
  {
    $condition = "WHERE id = $id";
    $sql = "DELETE FROM {$this->_tableName} $condition";
    return $this->queryExecute($sql);
  }

  protected function queryExecute($sql)
  {
    // Create connection
    $conn = new mysqli($this->_env['host'], $this->_env['username'], $this->_env['password'], $this->_env['dbname']);
    $conn->set_charset($this->_env['charset'] ?: 'utf8');

    // Check connection
    if ($conn->connect_error) {
      return [
        'error' => [
          'message' => 'Connection failed: ' . $conn->connect_error,
          'host' => $this->_env['host'],
          'dbname' => $this->_env['dbname'],
          'username' => $this->_env['username'],
        ]
      ];
    }

    $retValue = null;
    $result = $conn->query($sql);
    if ($result === TRUE) {
      $retValue = ['success' => 'query successfully', 'insert_id' => $conn->insert_id];
    } else if (gettype($result) == 'object' && $result->num_rows >= 0) {
      $retValue = [];
      // output data of each row
      while ($row = $result->fetch_assoc()) {
        array_push($retValue, $row);
      }
    } else {
      $retValue = [
        'error' => [
          'sql' => $sql,
          'message' => $conn->error
        ]
      ];
    }

    $conn->close();
    $this->initSql();

    return $retValue;
  }
}
