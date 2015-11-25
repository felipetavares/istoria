<?php

// Object holding information
// about a specific edit
class EditObj {
  public $id = 0;
  public $user = 0;
  public $info = 0;
  public $name = "";
  public $content = "";
  public $published = 0;

  function __construct ($arr) {
    $this->id = $arr['id'];
    $this->user = $arr['user'];
    $this->info = $arr['info'];
    $this->name = $arr['name'];
    $this->content = $arr['content'];
    $this->published = $arr['published'];
  }

  public function set ($id, $user, $info, $name, $content, $published) {
    $this->id = $id;
    $this->user = $user;
    $this->info = $info;
    $this->name = $name;
    $this->content = $content;
    $this->published = $published;
  }
}

// For low_level manipulation of the
// edit table
class edit {
  static function add ($user, $info, $name, $content, $published) {
    $query  = "INSERT INTO edit VALUES (";
    $query .= "null,";
    $query .= "\"{$user}\",";
    $query .= "\"{$info}\",";
    $query .= "\"{$name}\",";
    $query .= "\"{$content}\",";
    $query .= "\"{$published}\"";
    $query .= ");";

    if (!$GLOBALS['C']->query($query)) {
      print_query_error();
      return false;
    } else {
      return edit::get($GLOBALS['C']->insert_id);
    }
  }

  static function update ($id, $user, $info, $name, $content, $published) {
    $query  = "UPDATE edit SET ";
    $query .= "user=\"{$user}\" ";
    $query .= "info=\"{$info}\" ";
    $query .= "name=\"{$name}\" ";
    $query .= "content=\"{$content}\" ";
    $query .= "published=\"{$published}\" ";
    $query .= "WHERE id={$id};";

    if (!$GLOBALS['C']->query($query)) {
      print_query_error();
      return false;
    } else {
      return true;
    }
  }

  // HACK: Probably we do not need this function
  static function update_user ($id, $user) {
    $query  = "UPDATE edit SET ";
    $query .= "user=\"{$user}\" ";
    $query .= "WHERE id={$id};";

    if (!$GLOBALS['C']->query($query)) {
      print_query_error();
      return false;
    } else {
      return true;
    }
  }

  // HACK: Probably we do not need this function
  static function update_info ($id, $info) {
    $query  = "UPDATE edit SET ";
    $query .= "info=\"{$info}\" ";
    $query .= "WHERE id={$id};";

    if (!$GLOBALS['C']->query($query)) {
      print_query_error();
      return false;
    } else {
      return true;
    }
  }

  static function update_name ($id, $name) {
    $query  = "UPDATE edit SET ";
    $query .= "name=\"{$name}\" ";
    $query .= "WHERE id={$id};";

    if (!$GLOBALS['C']->query($query)) {
      print_query_error();
      return false;
    } else {
      return true;
    }
  }

  static function update_content ($id, $content) {
    $query  = "UPDATE edit SET ";
    $query .= "content=\"{$content}\" ";
    $query .= "WHERE id={$id};";

    if (!$GLOBALS['C']->query($query)) {
      print_query_error();
      return false;
    } else {
      return true;
    }
  }

  static function update_published ($id, $published) {
    $query  = "UPDATE edit SET ";
    $query .= "published=\"{$published}\" ";
    $query .= "WHERE id={$id};";

    if (!$GLOBALS['C']->query($query)) {
      print_query_error();
      return false;
    } else {
      return true;
    }
  }

  static function delete ($id) {
    $query  = "DELETE FROM edit WHERE ";
    $query .= "id={$id};";

    if (!$GLOBALS['C']->query($query)) {
      print_query_error();
      return false;
    } else {
      return true;
    }
  }

  static function get_all () {
    $query = "SELECT * FROM edit;";
    $result = null;

    if (!($result = $GLOBALS['C']->query($query))) {
      print_query_error();
      return false;
    } else {
      $list = array();

      while ($row = $result->fetch_assoc()) {
        array_push($list, new EditObj($row));
      }

      return $list;
    }
  }

  static function get_all_from_user ($user) {
    $query = "SELECT * FROM edit WHERE ";
    $query .= "user={$user};";
    $result = null;

    if (!($result = $GLOBALS['C']->query($query))) {
      print_query_error();
      return false;
    } else {
      $list = array();

      while ($row = $result->fetch_assoc()) {
        array_push($list, new EditObj($row));
      }

      return $list;
    }
  }

  static function get_from_user_info ($user, $info) {
    $query = "SELECT * FROM edit WHERE ";
    $query .= "user={$user} AND info={$info};";
    $result = null;

    if (!($result = $GLOBALS['C']->query($query))) {
      print_query_error();
      return false;
    } else {
      if ($row = $result->fetch_assoc()) {
        return new EditObj($row);
      } else {
        return false;
      }
    }
  }

  static function get ($id) {
    $query  = "SELECT * FROM edit WHERE ";
    $query .= "id={$id};";
    $result = null;

    if (!($result = $GLOBALS['C']->query($query))) {
      print_query_error();
      return false;
    } else {
      if ($row = $result->fetch_assoc()) {
        return new EditObj($row);
      } else {
        return false;
      }
    }
  }

  static function related ($id) {
    $query  = "SELECT * FROM edit_info WHERE ";
    $query .= "edit={$id};";
    $result = null;

    if (!($result = $GLOBALS['C']->query($query))) {
      print_query_error();
      return false;
    } else {
      $list = array();

      while ($row = $result->fetch_assoc()) {
        // ID of the related edit
        $r_id = $row['info'];

        array_push($list, info::get($r_id));
      }

      return $list;
    }
  }

  static function relation_exists ($edit, $info) {
    $query  = "SELECT * FROM edit_info WHERE ";
    $query .= "edit={$edit} AND info={$info};";
    $result = null;

    if (!($result = $GLOBALS['C']->query($query))) {
      print_query_error();
      return false;
    } else {
      if ($row = $result->fetch_assoc()) {
        return true;
      } else {
        return false;
      }
    }
  }

  static function add_relation ($edit, $info) {
    if (edit::relation_exists($edit, $info)) {
      return false;
    }

    $query  = "INSERT INTO edit_info VALUES (";
    $query .= "\"{$edit}\",";
    $query .= "\"{$info}\"";
    $query .= ");";

    if (edit::get($edit)->info != $info) {
      if (!$GLOBALS['C']->query($query)) {
        print_query_error();
        return false;
      } else {
        return true;
      }
    } else {
      return false;
    }
  }

  static function delete_relation ($edit, $info) {
    if (edit::relation_exists($edit, $info)) {
      $query  = "DELETE FROM edit_info WHERE ";
      $query .= "edit={$edit} AND info={$info};";

      if (!$GLOBALS['C']->query($query)) {
        print_query_error();
        return false;
      } else {
        return true;
      }
    } else {
      return false;
    }
  }

  static function delete_all_relations ($edit) {
    $query  = "DELETE FROM edit_info WHERE ";
    $query .= "edit={$edit};";

    if (!$GLOBALS['C']->query($query)) {
      print_query_error();
      return false;
    } else {
      return true;
    }
  }
}
