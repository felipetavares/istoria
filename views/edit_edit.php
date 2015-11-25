<?php
  session_start();

  include '../db/db.php';

  if (!isset($_SESSION['user_id'])) {
    die();
  }

  $user = user::get($_SESSION['user_id']);

  if (!isset($_GET['id']) &&
      (!isset($_POST['edit_name']) ||
      !isset($_POST['edit_content']) ||
      !isset($_POST['edit_id']))) {
    die();
  }

  $edit = null;

  if (isset($_POST['edit_id'])) {
    $edit = edit::get($_POST['edit_id']);
    $name = $_POST['edit_name'];
    $content = $_POST['edit_content'];
    $rel = $_POST['edit_related'];

    if ($edit->user == $user->id) {
      edit::update_name($edit->id, $name);
      edit::update_content($edit->id, $content);
      edit::add_relation($edit->id, $rel);

      $edit = edit::get($_POST['edit_id']);
    }
  } else {
    $edit = edit::get($_GET['id']);
  }
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

    <main>
      <div class="ist-editor">
        <form action="" method="post">
          <input type="hidden" name="edit_id" value="<?php echo $edit->id; ?>">
          <input name="edit_name" value="<?php echo $edit->name; ?>" type="text">
          <textarea name="edit_content"><?php echo $edit->content; ?></textarea>
          <select name="edit_related">
            <?php
            foreach (info::get_all() as $i) {
            ?>
            <option value="<?php echo $i->id; ?>"><?php echo $i->name; ?></option>
            <?php
            }
            ?>
          </select>
          <?php
          foreach (edit::related($edit->id) as $r) {
            echo $r->id.$r->name;
          }
          ?>
          <input type="submit" value="Salvar">
        </form>
      </div>
    </main>
  </body>
</html>
