<?php

// Object holding information
// about a specific notification
class NotificationObj {
  public $id = 0;
  public $user = 0;
  public $content = "";
  public $viewed = 0;

  function __construct ($arr) {
    $this->id = $arr['id'];
    $this->user = $arr['user'];
    $this->content = $arr['content'];
    $this->viewed = $arr['viewed'];
  }

  public function set ($id, $user, $content, $viewed) {
    $this->id = $id;
    $this->user = $user;
    $this->content = $content;
    $this->viewed = $viewed;
  }
}

// For low_level manipulation of the
// notifications table
class notifications {
  static function add ($user, $content) {
    $query  = "INSERT INTO notifications VALUES (0, ";
    $query .= "\"{$user}\",";
    $query .= "\"{$content}\",";
    $query .= "0";
    $query .= ");";

    if (!$GLOBALS['C']->query($query)) {
      print_query_error();
      return false;
    } else {
      return notifications::get($GLOBALS['C']->insert_id);
    }
  }

  static function view ($id) {
    $query  = "UPDATE notifications SET ";
    $query .= "viewed=1 ";
    $query .= "WHERE id={$id};";

    if (!$GLOBALS['C']->query($query)) {
      print_query_error();
      return false;
    } else {
      return true;
    }
  }

  static function delete ($id) {
    $query  = "DELETE FROM notifications WHERE ";
    $query .= "id=${id};";

    if (!$GLOBALS['C']->query($query)) {
      print_query_error();
      return false;
    } else {
      return true;
    }
  }

  static function count ($user) {
    $query  = "SELECT COUNT(*) AS count FROM notifications WHERE ";
    $query .= "user={$user} AND notifications.viewed=0;";
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

  static function get_for_user ($user) {
    $query  = "SELECT * FROM notifications WHERE ";
    $query .= "user={$user} ORDER BY id DESC;";
    $result = null;

    if (!($result = $GLOBALS['C']->query($query))) {
      print_query_error();
      return false;
    } else {
      $list = array();

      while ($row = $result->fetch_assoc()) {
        array_push($list, new NotificationObj($row));
      }

      return $list;
    }
  }

  static function get ($id) {
    $query  = "SELECT * FROM notifications WHERE ";
    $query .= "id=${id};";
    $result = null;

    if (!($result = $GLOBALS['C']->query($query))) {
      print_query_error();
      return false;
    } else {
      if ($row = $result->fetch_assoc()) {
        return new NotificationObj($row);
      } else {
        return false;
      }
    }
  }
}
