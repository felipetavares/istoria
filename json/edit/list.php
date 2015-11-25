<?php
  session_start();
  include '../../db/db.php';
  include '../../php/Parsedown.php';
  include '../message.php';
  $Parsedown = new Parsedown();

  // Cinco resultados por página
  $per_page = 5;
  $max_per_page = 50;

  // página (p)
  if (isset($_GET['p'])) {
    if (isset($_GET['n'])) {
      if ($_GET['n'] < $max_per_page) {
        $per_page = $_GET['n'];
      } else {
        $per_page = $max_per_page;
      }
    }

    if (count(edit::get_all()) == 0) {
      echo json_encode(api_success(array()));
    } else {
      $data = edit::get_all();

      if ($data) {
        $page = array();
        $start = $_GET['p']*$per_page;

        for ($i=0;$i<$per_page && $start+$i < count($data);$i++) {
          $edit = $data[$i+$start];

          $edit->user = user::get($edit->user);
          $edit->votes = votes::count($edit->id);
          $edit->total = count(user::get_all())*0.1;

          array_push($page, $edit);
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
