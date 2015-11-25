<?php

// Object holding information
// about a specific info
class InfoObj {
  public $id = 0;
  public $name = "";
  public $content = "";

  function __construct ($arr) {
    $this->id = $arr['id'];
    $this->name = $arr['name'];
    $this->content = $arr['content'];
  }

  public function set ($id, $name, $content) {
    $this->id = $id;
    $this->name = $name;
    $this->content = $content;
  }
}

// For low_level manipulation of the
// info table
class info {
  static function add ($name, $content) {
    $query  = "INSERT INTO info VALUES (";
    $query .= "null,";
    $query .= "\"{$name}\",";
    $query .= "\"{$content}\"";
    $query .= ");";

    if (!$GLOBALS['C']->query($query)) {
      print_query_error();
      return false;
    } else {
      return info::get($GLOBALS['C']->insert_id);
    }
  }

  static function update ($id, $name, $content) {
    $query  = "UPDATE info SET ";
    $query .= "name=\"{$name}\", ";
    $query .= "content=\"{$content}\" ";
    $query .= "WHERE id={$id};";

    if (!$GLOBALS['C']->query($query)) {
      print_query_error();
      return false;
    } else {
      return true;
    }
  }

  static function update_name ($id, $name) {
    $query  = "UPDATE info SET ";
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
    $query  = "UPDATE info SET ";
    $query .= "content=\"{$content}\" ";
    $query .= "WHERE id={$id};";

    if (!$GLOBALS['C']->query($query)) {
      print_query_error();
      return false;
    } else {
      return true;
    }
  }

  static function delete ($id) {
    $query  = "DELETE FROM info WHERE ";
    $query .= "id={$id};";

    if (!$GLOBALS['C']->query($query)) {
      print_query_error();
      return false;
    } else {
      return true;
    }
  }

  static function get_all () {
    $query = "SELECT * FROM info;";
    $result = null;

    if (!($result = $GLOBALS['C']->query($query))) {
      print_query_error();
      return false;
    } else {
      $list = array();

      while ($row = $result->fetch_assoc()) {
        array_push($list, new InfoObj($row));
      }

      return $list;
    }
  }

  static function get ($id) {
    $query  = "SELECT * FROM info WHERE ";
    $query .= "id={$id};";
    $result = null;

    if (!($result = $GLOBALS['C']->query($query))) {
      print_query_error();
      return false;
    } else {
      if ($row = $result->fetch_assoc()) {
        return new InfoObj($row);
      } else {
        return false;
      }
    }
  }

  static function get_from_name ($name) {
    $query  = "SELECT * FROM info WHERE ";
    $query .= "name=\"{$name}\";";
    $result = null;

    if (!($result = $GLOBALS['C']->query($query))) {
      print_query_error();
      return false;
    } else {
      if ($row = $result->fetch_assoc()) {
        return new InfoObj($row);
      } else {
        return false;
      }
    }
  }

  static function search ($name) {
    $query  = "SELECT * FROM info WHERE ";
    $query .= "name LIKE \"%{$name}%\";";
    $result = null;

    if (!($result = $GLOBALS['C']->query($query))) {
      print_query_error();
      return false;
    } else {
      $list = array();

      while ($row = $result->fetch_assoc()) {
        array_push ($list, new InfoObj($row));
      }

      return $list;
    }
  }

  static function related ($id) {
    $query  = "SELECT * FROM info_info WHERE ";
    $query .= "id_a={$id} OR id_b={$id};";
    $result = null;

    if (!($result = $GLOBALS['C']->query($query))) {
      print_query_error();
      return false;
    } else {
      $list = array();

      while ($row = $result->fetch_assoc()) {
        // ID of the related info
        $r_id = 0;

        if ($row['id_a'] == $id) {
          $r_id = $row['id_b'];
        } else {
          $r_id = $row['id_a'];
        }

        array_push($list, info::get($r_id));
      }

      return $list;
    }
  }

  static function relation_exists ($id_a, $id_b) {
    $query  = "SELECT * FROM info_info WHERE ";
    $query .= "(id_a={$id_a} AND id_b={$id_b}) OR ";
    $query .= "(id_a={$id_b} AND id_b={$id_a});";
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

  static function add_relation ($id_a, $id_b) {
    if (info::relation_exists($id_a, $id_b)) {
      return false;
    }

    $query  = "INSERT INTO info_info VALUES (";
    $query .= "\"{$id_a}\",";
    $query .= "\"{$id_b}\"";
    $query .= ");";

    if (!$GLOBALS['C']->query($query)) {
      print_query_error();
      return false;
    } else {
      return true;
    }
  }

  static function delete_relation ($id_a, $id_b) {
    if (info::relation_exists($id_a, $id_b)) {
      $query  = "DELETE FROM info_info WHERE ";
      $query .= "(id_a={$id_a} AND id_b={$id_b}) OR ";
      $query .= "(id_a={$id_b} AND id_b={$id_a});";

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

  static function delete_all_relations ($id_a) {
    $query  = "DELETE FROM info_info WHERE ";
    $query .= "id_a={$id_a} OR ";
    $query .= "id_b={$id_a};";

    if (!$GLOBALS['C']->query($query)) {
      print_query_error();
      return false;
    } else {
      return true;
    }
  }
}
