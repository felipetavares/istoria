<?php
  session_start();

  include '../db/db.php';

  $edit = edit::get($_GET['id']);
  $user = user::get($_SESSION['user_id']);

  votes::delete($user->id, $edit->id);

  header("Location: vote.php");
?>
