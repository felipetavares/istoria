<?php

// Object holding information
// about a specific vote
class VoteObj {
  public $user = 0;
  public $edit = "";

  function __construct ($arr) {
    $this->user = $arr['user'];
    $this->edit = $arr['edit'];
  }

  public function set ($user, $edit) {
    $this->user = $user;
    $this->edit = $edit;
  }
}

// For low_level manipulation of the
// votes table
class votes {
  static function add ($user, $edit) {
    $query  = "INSERT INTO votes VALUES (";
    $query .= "\"{$user}\",";
    $query .= "\"{$edit}\"";
    $query .= ");";

    if (!$GLOBALS['C']->query($query)) {
      print_query_error();
      return false;
    } else {
      return true;
    }
  }


  static function delete ($user, $edit) {
    $query  = "DELETE FROM votes WHERE ";
    $query .= "user={$user} AND edit={$edit};";

    if (!$GLOBALS['C']->query($query)) {
      print_query_error();
      return false;
    } else {
      return true;
    }
  }

  static function count ($edit) {
    $query  = "SELECT COUNT(*) AS count FROM votes WHERE ";
    $query .= "edit={$edit};";
    $result = null;

    if (!($result = $GLOBALS['C']->query($query))) {
      print_query_error();
      return false;
    } else {
      if ($row = $result->fetch_assoc()) {
        return $row['count'];
      }
      return false;
    }
  }

  static function get ($edit) {
    $query  = "SELECT * FROM votes WHERE ";
    $query .= "edit={$edit};";
    $result = null;

    if (!($result = $GLOBALS['C']->query($query))) {
      print_query_error();
      return false;
    } else {
      $list = array();

      while ($row = $result->fetch_assoc()) {
        array_push($list, new VoteObj($row));
      }

      return $list;
    }
  }
}
