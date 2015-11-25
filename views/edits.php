<?php
  session_start();
  include '../db/db.php';

  if (!isset($_SESSION['user_id'])) {
    die();
  }

  $user = user::get($_SESSION['user_id']);
?>

<!doctype HTML>

<html>
  <head>
    <title>ιστορία</title>
    <meta charset="utf-8">
    <style>
      /* css principal */
      @import url(../css/main.css);
      @import url(../css/login.css);
      @import url(../css/vote.css);
    </style>
    <script src="../js/jquery.js"></script>
    <script src="../js/vote.js"></script>
  </head>

  <body>
    <div id="ist-barra">
      <h1><?php echo $user->name; ?></h1>
      <a href="logout.php"><img id="ist-login" title="Sair" src="../img/login.png" width=32></a>
    </div>

    <main>
      <div class="ist-vote-main">

      <?php
        if (count(edit::get_all_from_user($user->id)) == 0) {
            echo "<h2>";
            echo "Você não tem nenhuma edição.";
            echo "</h2>";
        } else
          foreach (edit::get_all_from_user($user->id) as $e) {
      ?>

        <div class="ist-vote-article">
          <?php echo "E#".$e->id . ": " . $e->name; ?>

          <span class="ist-article-link-2 ist-preview">Detalhes</span>

          <!--
          <div class="ist-bt-vote-up">
          </div>

          <div class="ist-bt-vote-down">
          </div>
          -->
        </div>

        <div class="ist-desc-box">
          <img src="../img/arrow.png" class="arrow-center">

          <div class="ist-article-desc">
            <h1><?php echo $e->name; ?></h1>

            <p>
              <?php echo $e->content; ?>
            </p>

            <a class="ist-article-link" target="_blank" href="edit_edit.php?id=<?php echo $e->id; ?>">Editar</a>
            <!-- <a href="#" class="ist-article-link">Ler mais</a> -->
          </div>
        </div>

      <?php
          }
      ?>

      </div>
    </main>
  </body>
</html>
