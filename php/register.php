<?php

include '../db/db.php';

function register_user ($name, $email, $pass) {
  $dificuldade = 8;

  // Salted hash
  $salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
  $salt = sprintf("$2a$%02d$", $dificuldade) . $salt;

  $hash = crypt($pass, $salt);

  return user::add($name, $email, $hash);
}
