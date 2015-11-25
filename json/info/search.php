<?php
  session_start();
  include '../../db/db.php';
  include '../../php/Parsedown.php';
  include '../message.php';
  $Parsedown = new Parsedown();

  // Cinco resultados por página
  $per_page = 5;
  $max_per_page = 50;

  // Termo de pesquisa (s) e página (p)
  if (isset($_GET['s']) && isset($_GET['p'])) {
    if (isset($_GET['n'])) {
      if ($_GET['n'] < $max_per_page) {
        $per_page = $_GET['n'];
      } else {
        $per_page = $max_per_page;
      }
    }

    if (count(info::get_all()) == 0) {
      echo json_encode(api_success(array()));
    } else {
      $data = info::search($_GET['s']);

      if ($data) {
        $page = array();
        $start = $_GET['p']*$per_page;

        for ($i=0;$i<$per_page && $start+$i < count($data);$i++) {
          if (isset($_SESSION['user_id'])) {
            if ($edit = edit::get_from_user_info($_SESSION['user_id'], $data[$i+$start]->id)) {
              $data[$i+$start]->content = $edit->content;
              $data[$i+$start]->name = $edit->name;
              $data[$i+$start]->edit = edit::get($edit->id);
            }
          }

          $data[$i+$start]->content = $Parsedown->text($data[$i+$start]->content);

          array_push($page, $data[$i+$start]);
        }

        if (count($page) > 0) {
          echo json_encode(api_success($page, count($page)+$start<count($data)));
        } else {
          // End Of Data
          echo json_encode(api_error('EOD'));
        }
      } else {
        echo json_encode(api_error('Info not found'));
      }
    }
  } else {
    echo json_encode(api_error('Insufficient parameters'));
  }
?>
