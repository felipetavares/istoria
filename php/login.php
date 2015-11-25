<?php

include '../db/db.php';

function verify_user ($email, $pass) {
  $user = user::get_from_email($email);
  $upass = $user->pass;

  if ($upass == crypt($pass, $upass)) {
    return true;
  } else {
    return false;
  }
}
