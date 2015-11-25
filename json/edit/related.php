<?php
include '../../db/db.php';
include '../../php/Parsedown.php';
include '../message.php';
include '../apply.php';
$Parsedown = new Parsedown();

if (isset($_GET['id'])) {
  if (count(edit::get_all()) == 0) {
    echo json_encode(api_error('edit not found'));
  } else {
    $data = null;

    if (isset($_GET['simulate']))
      $data = simulate_edit(edit::get($_GET['id']));
    else
      $data = apply_edit(edit::get($_GET['id']));

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
