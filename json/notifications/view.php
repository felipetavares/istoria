<?php
  include '../../db/db.php';
  include '../message.php';

  //session_start();
  //TODO: Add security
  //if (isset($_SESSION['user_id'])) {

  if (isset($_GET['id'])) {
    $data = notifications::view($_GET['id']);

    if ($data) {
      echo json_encode(api_success($data));
    } else {
      echo json_encode(api_error(false));
    }
  } else {
    echo json_encode(api_error('Insufficient parameters'));
  }
?>
