<?php
  session_start();
  include '../../db/db.php';
  include '../../php/Parsedown.php';
  include '../message.php';
  include '../../php/diff.php';

  $Parsedown = new Parsedown();

  if (isset($_GET['id'])) {
    $data = edit::get($_GET['id']);
    $info = info::get($data->info);

    if ($data) {
        if (!isset($_GET['markdown'])) {
          $data->content = $Parsedown->text($data->content);
          $data->diff = createDiff($Parsedown->text($info->content), $data->content);
        }
        echo json_encode(api_success($data));
    } else {
      echo json_encode(api_error('Info not found'));
    }
  } else {
    echo json_encode(api_error('Insufficient parameters'));
  }
?>
