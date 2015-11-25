<?php
  include '../../db/db.php';
  include '../../php/Parsedown.php';
  include '../message.php';
  $Parsedown = new Parsedown();

  if (isset($_GET['id'])) {
    if (count(info::get_all()) == 0) {
      echo json_encode(api_error('Info not found'));
    } else {
      $data = info::get($_GET['id']);

      if ($data) {
        session_start();

        if (isset($_SESSION['user_id'])) {
          if ($edit = edit::get_from_user_info($_SESSION['user_id'], $data->id)) {
            $data->content = $edit->content;
            $data->name = $edit->name;
          }
        }

        if (!isset($_GET['markdown'])) {
          $data->content = $Parsedown->text($data->content);
        }

        echo json_encode(api_success($data));
      } else {
        echo json_encode(api_error('Info not found'));
      }
    }
  } else {
    echo json_encode(api_error('Insufficient parameters'));
  }
?>
