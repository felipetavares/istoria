<?php
  session_start();

  include '../db/db.php';

  if (isset($_POST['info_name']) &&
      isset($_POST['info_content'])) {
    $name = $_POST['info_name'];
    $content = $_POST['info_content'];

    if (info::add($name, $content)) {
      echo "Adicionado";
    } else {
      echo "Não foi p. adc";
    }
  }

  $user = null;

  if (isset($_SESSION['user_id']))
    $user = user::get($_SESSION['user_id']);
?>

<!doctype HTML>

<html>
  <head>
    <meta charset="utf-8">
    <title>Adicionar</title>
    <style>
      /* css principal */
      @import url(../css/main.css);
      @import url(../css/login.css);
      @import url(../css/editor.css);
    </style>
    <script src="../js/jquery.js"></script>
    <script src="../js/vote.js"></script>
  </head>

  <body>
    <div id="ist-barra">
      <h1><?php echo $user->name; ?></h1>
      <a href="logout.php"><img id="ist-login" title="Sair" src="../img/login.png" width=32></a>
    </div>

    <h2>
      <a href="home.php">Todos os fatos</a>
    </h2>

    <main>
      <div class="ist-editor">
        <form action="" method="post">
          <input name="info_name" value="" type="text">
          <textarea name="info_content"></textarea>
          <input type="submit" value="Adicionar">
        </form>
      </div>
    </main>
  </body>
</html>
