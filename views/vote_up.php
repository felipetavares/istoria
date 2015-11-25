<?php
  include '../json/apply.php';
  ini_set('display_errors', 1);
  error_reporting(~0);
  session_start();

  include '../db/db.php';

  $edit = edit::get($_GET['id']);
  $user = user::get($_SESSION['user_id']);

  votes::add($user->id, $edit->id);

  if (votes::count($edit->id) >= user::count()*0.1) {
    // Delete votes
    foreach (votes::get($edit->id) as $v) {
      votes::delete($v->user, $v->edit);
    }

    // Add a notification
    notifications::add($edit->user, "<b>(E#".$edit->id.")</b> Sua edição em <em>".$edit->name."</em> foi aprovada.");

    info::update_name($edit->info, $edit->name);
    info::update_content($edit->info, $edit->content);

    $related = apply_edit($edit);

    info::delete_all_relations($edit->info);
    foreach ($related as $i) {
      info::add_relation($edit->info, $i->id);
    }
  }

  edit::delete_all_relations($edit->id);
  edit::delete($edit->id);

  header("Location: vote.php");
?>
