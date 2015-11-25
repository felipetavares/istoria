<?php
  include '../../db/db.php';
  include '../message.php';

  //session_start();
  //TODO: Add security
  //if (isset($_SESSION['user_id'])) {

  if (isset($_GET['user'])) {
    $data = notifications::get_for_user($_GET['user']);

    if ($data) {
      echo json_encode(api_success($data));
    } else {
      echo json_encode(api_error('0'));
    }
  } else {
    echo json_encode(api_error('Insufficient parameters'));
  }
?>
