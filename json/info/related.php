<?php
  include '../../db/db.php';
  include '../../php/Parsedown.php';
  include '../message.php';
  include '../apply.php';
  $Parsedown = new Parsedown();

  if (isset($_GET['id'])) {
    if (count(info::get_all()) == 0) {
      echo json_encode(api_error('Info not found'));
    } else {
      $data = info::related($_GET['id']);

      session_start();

      if (isset($_SESSION['user_id'])) {
        if ($edit = edit::get_from_user_info($_SESSION['user_id'], $_GET['id'])) {
          $data = apply_edit($edit);
        }
      }

      if (!isset($_GET['markdown'])) {
        foreach ($data as $d) {
          $d->content = $Parsedown->text($d->content);
        }
      }

      echo json_encode(api_success($data));
    }
  } else {
    echo json_encode(api_error('Insufficient parameters'));
  }
?>
