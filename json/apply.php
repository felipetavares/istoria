<?php
function id_in_array ($array, $id) {
  $position = 0;
  foreach ($array as $element) {
    if ($element->id == $id)
      return $position;
    $position++;
  }
  return -1;
}

function apply_edit ($edit) {
  $info = info::get($edit->info);
  $changes = edit::related($edit->id);
  $related = info::related($info->id);

  foreach ($changes as $change) {
    $position = 0;
    if (($position = id_in_array($related, $change->id)) >= 0) {
      array_splice($related, $position, 1);
    } else {
      array_push($related, info::get($change->id));
    }
  }

  return $related;
}

function simulate_edit ($edit) {
  $info = info::get($edit->info);
  $changes = edit::related($edit->id);
  $related = info::related($info->id);

  foreach ($changes as $change) {
    $position = 0;
    if (($position = id_in_array($related, $change->id)) >= 0) {
      $related[$position]->removed = true;
    } else {
      $new = info::get($change->id);
      $new->added = true;
      array_push($related, $new);
    }
  }

  return $related;
}
