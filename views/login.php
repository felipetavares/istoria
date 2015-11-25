<?php
  session_start();
  include '../php/login.php';

  function go_to_homepage () {
    header("Location: home.php");
    die();
  }

  if (isset($_SESSION['user_id'])) {
    go_to_homepage();
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
    </style>
    <script src="../js/jquery.js"></script>
    <script src="../js/login.js"></script>
  </head>

  <body>
    <?php
    if ((isset($_POST['user_email']) &&
        isset($_POST['user_pass'])) ||
        (isset($_POST['e_user_email']) &&
        isset($_POST['e_user_pass']))) {
      $email = null;
      $pass = null;

      if (isset($_POST['user_email'])) {
        $email = $_POST['user_email'];
        $pass = $_POST['user_pass'];
      } else {
        $email = $_POST['e_user_email'];
        $pass = $_POST['e_user_pass'];
      }

      if (verify_user($email, $pass)) {
        $user = user::get_from_email($email);

        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_name'] = $user->name;
        $_SESSION['user_id'] = $user->id;

        go_to_homepage();
      } else {
        echo "Senha e/ou email inválidos";
      }
    }
    ?>

    <div id="ist-barra">
      <h1>ιστορία</h1>
      <img id="ist-login" title="Entrar" src="../img/login.png" width=48>
    </div>

    <div id="entrar-dialogo" class="caixa-login">
      <img src="../img/arrow.png" class="arrow-right">

      <form action="" method="post">
        <div>
          <label for="e_user_email">
            email:
          </label>
          <input name="e_user_email" type="text" data-formato="[a-z]+\.[a-z]+(\.[a-z]+)?">
        </div>
        <div>
          <label for="e_user_pass">
            senha:
          </label>
          <span><input name="e_user_pass" type="password" data-formato="[a-zA-Z0-9_\\\/]{6,}"></span>
        </div>
        <div>
          <input id="entrar" value="Entrar" type="submit">
        </div>
      </form>
    </div>

    <div class="clear"></div>

    <main>
      <h2>(Re)construindo a história, fato por fato</h2>

      <p>
        O nome <em>ιστορία</em>, pronunciado <em>istoría</em>, em grego significa <em>história</em>.
        Aqui buscamos reconstruir a história global, em todas as suas nuances e detalhes.
      </p>

      <div class="links">
        <a href="graph.php?tour">
          <div class="ist-botao">
            Faça um tour
          </div>
        </a>

<a href="help.php">
        <div class="ist-botao">
          Mais informações</a>
        </div>
      </div>

      <h2>Torne-se um colaborador</h2>

      <img class="ist-avatar" src="../img/istoría-logo-1.png">

      <div class="caixa-login">
        <img src="../img/arrow.png" class="arrow-center">

        <form action="register.php" method="post">
          <div>
            <label for="user_name">
              nome:
            </label>
            <input name="user_name" type="text" data-formato="[a-z]+\.[a-z]+(\.[a-z]+)?">
          </div>
          <div>
            <label for="user_email">
              email:
            </label>
            <input name="user_email" type="text" data-formato="[a-z]+\.[a-z]+(\.[a-z]+)?">
          </div>
          <div>
            <label for="user_pass">
              senha:
            </label>
            <span><input name="user_pass" type="password" data-formato="[a-zA-Z0-9_\\\/]{6,}"></span>
          </div>
          <div>
            <label for="user_accept">
              aceito os termos abaixo:
            </label>
            <span><input name="user_accept" type="checkbox"></span>
          </div>
          <div>
            <input id="entrar" value="Registrar" type="submit">
          </div>
        </form>
      </div>

      <p>
        Para se tornar um colaborador, você precisa concordar com os seguintes termos:

        <ol>
          <li>não utilizar seus direitos de editor para fins que possam causar danos a propriedade ou pessoas;</li>
          <li>não insultar ou agredir de qualquer forma outros editores do <em>ιστορία</em>;</li>
          <li>não utilizar o sistema de forma indevida ou inapropriada.</li>
        </ol>
      </p>
    </main>
  </body>
</html>
