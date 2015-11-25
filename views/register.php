<?php
  session_start();

  include '../php/register.php';

  function go_to_homepage () {
    header("Location: home.php");
    die();
  }

  if (isset($_POST['user_email']) &&
      isset($_POST['user_pass']) &&
      isset($_POST['user_name'])) {

    $name = $_POST['user_name'];
    $email = $_POST['user_email'];
    $pass = $_POST['user_pass'];

    if ($user = register_user($name, $email, $pass)) {
      $_SESSION['user_email'] = $user->email;
      $_SESSION['user_name'] = $user->name;
      $_SESSION['user_id'] = $user->id;

      go_to_homepage();
    } else {
      echo "Problemas ao registrar usuário.";
    }
  }
?>
