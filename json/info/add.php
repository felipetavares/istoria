<?php
include '../../db/db.php';
include '../message.php';

if (isset($_GET['name']) &&
    isset($_GET['content']) &&
    isset($_GET['related']) &&
    isset($_GET['published'])) {
  $name = $_GET['name'];
  $content = $_GET['content'];
  $published = $_GET['published'];

  if (isset($_GET['id'])) {
      $relations = json_decode($_GET['related']);

      session_start();

      if (!isset($_SESSION['user_id']))
        die();

      $user = user::get($_SESSION['user_id']);
      $info = info::get($_GET['id']);

      if ($edit = edit::get_from_user_info($_SESSION['user_id'], $info->id)) {
        edit::update_name($edit->id, $name);
        edit::update_content($edit->id, $content);
        edit::update_published($edit->id, $published);

        // Remove from $relations if exists in the current relations of the edit
        $edit_relations = edit::related($edit->id);

        foreach ($edit_relations as $er) {
          $position = 0;
          foreach ($relations as $r) {
            if ($er->id == $r) {
              array_splice($relations, $position, 1);
              edit::delete_relation($edit->id, $er->id);
            }
            $position++;
          }
        }
      } else {
        $edit = edit::add($user->id, $info->id, $name, $content, $published);
      }

      foreach ($relations as $relation) {
        edit::add_relation($edit->id, $relation);
      }

      echo json_encode(api_success('Success'));
  } else {
    if ($info = info::add($name, $content)) {
      $relations = json_decode($_GET['related']);

      foreach ($relations as $relation) {
        info::add_relation($info->id, $relation);
      }

      echo json_encode(api_success('Sucess'));
    } else {
      echo json_encode(api_error('Could not add to the database'));
    }
  }
} else {
    echo json_encode(api_error('Insufficient parameters'));
}
