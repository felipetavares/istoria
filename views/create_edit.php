<?php
  session_start();
  include '../db/db.php';

  if (!isset($_GET['id']) || !isset($_SESSION['user_id']))
    die();

  $user = user::get($_SESSION['user_id']);
  $info = info::get($_GET['id']);

  $edit = edit::add($user->id, $info->id, $info->name, $info->content);

  header("Location: vote.php");
