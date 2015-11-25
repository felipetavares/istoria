<?php

// Object holding information
// about a specific user
class UserObj {
  public $id = 0;
  public $name = "";
  public $email = "";
  public $pass = "";

  function __construct ($arr) {
    $this->id = $arr['id'];
    $this->name = $arr['name'];
    $this->email = $arr['email'];
    $this->pass = $arr['pass'];
  }

  public function set ($id, $name, $email, $pass) {
    $this->id = $id;
    $this->name = $name;
    $this->email = $email;
    $this->pass = $pass;
  }
}

// For low_level manipulation of the
// user table
class user {
  static function add ($name, $email, $pass) {
    $query  = "INSERT INTO user VALUES (";
    $query .= "null,";
    $query .= "\"{$name}\",";
    $query .= "\"{$email}\",";
    $query .= "\"{$pass}\"";
    $query .= ");";

    if (!$GLOBALS['C']->query($query)) {
      print_query_error();
      return false;
    } else {
      return user::get($GLOBALS['C']->insert_id);
    }
  }

  static function update ($id, $name, $email, $pass) {
    $query  = "UPDATE user SET ";
    $query .= "name=\"{$name}\" ";
    $query .= "email=\"{$email}\" ";
    $query .= "pass=\"{$pass}\" ";
    $query .= "WHERE id={$id};";

    if (!$GLOBALS['C']->query($query)) {
      print_query_error();
      return false;
    } else {
      return true;
    }
  }

  static function update_name ($id, $name) {
    $query  = "UPDATE user SET ";
    $query .= "name=\"{$name}\" ";
    $query .= "WHERE id={$id};";

    if (!$GLOBALS['C']->query($query)) {
      print_query_error();
      return false;
    } else {
      return true;
    }
  }

  static function update_email ($id, $email) {
    $query  = "UPDATE user SET ";
    $query .= "email=\"{$email}\" ";
    $query .= "WHERE id={$id};";

    if (!$GLOBALS['C']->query($query)) {
      print_query_error();
      return false;
    } else {
      return true;
    }
  }

  static function update_pass ($id, $pass) {
    $query  = "UPDATE user SET ";
    $query .= "pass=\"{$pass}\" ";
    $query .= "WHERE id={$id};";

    if (!$GLOBALS['C']->query($query)) {
      print_query_error();
      return false;
    } else {
      return true;
    }
  }

  static function delete ($id) {
    $query  = "DELETE FROM user WHERE ";
    $query .= "id={$id};";

    if (!$GLOBALS['C']->query($query)) {
      print_query_error();
      return false;
    } else {
      return true;
    }
  }

  static function get_all () {
    $query = "SELECT * FROM user;";
    $result = null;

    if (!($result = $GLOBALS['C']->query($query))) {
      print_query_error();
      return false;
    } else {
      $list = array();

      while ($row = $result->fetch_assoc()) {
        array_push($list, new UserObj($row));
      }

      return $list;
    }
  }

  static function get ($id) {
    $query  = "SELECT * FROM user WHERE ";
    $query .= "id={$id};";
    $result = null;

    if (!($result = $GLOBALS['C']->query($query))) {
      print_query_error();
      return false;
    } else {
      if ($row = $result->fetch_assoc()) {
        return new UserObj($row);
      } else {
        return false;
      }
    }
  }

  static function get_from_name ($name) {
    $query  = "SELECT * FROM user WHERE ";
    $query .= "name=\"{$name}\";";
    $result = null;

    if (!($result = $GLOBALS['C']->query($query))) {
      print_query_error();
      return false;
    } else {
      if ($row = $result->fetch_assoc()) {
        return new UserObj($row);
      } else {
        return false;
      }
    }
  }

  static function get_from_email ($email) {
    $query  = "SELECT * FROM user WHERE ";
    $query .= "email=\"{$email}\";";
    $result = null;

    if (!($result = $GLOBALS['C']->query($query))) {
      print_query_error();
      return false;
    } else {
      if ($row = $result->fetch_assoc()) {
        return new UserObj($row);
      } else {
        return false;
      }
    }
  }

  static function count () {
    $query  = "SELECT COUNT(*) AS count FROM user;";
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
}
