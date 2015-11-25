<?php
  session_start();
  include '../db/db.php';
  include '../php/Parsedown.php';
  $Parsedown = new Parsedown();

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
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="mobile-web-app-capable" content="yes">
    <style>
      @import url(../css/home.css);
    </style>
    <script src="../js/html5.js"></script>
    <script src="../js/jquery.js"></script>
    <script src="../js/jquery-ui.js"></script>
    <script src="../js/edit.js"></script>
    <script src="../js/sidebar.js"></script>
    <script src="../js/related.js"></script>
    <script src="../js/vote.js"></script>
    <script src="../js/notifications.js"></script>
  </head>

  <body>
    <div class="ist-barra">
      <div class="ist-home" title="Visualizar informações">
        <a href="home.php"><img src="../img/home.png" height=48></a>
      </div>
      <div class="ist-logout" title="Sair">
        <a href="logout.php"><img src="../img/logout.png" height=48></a>
      </div>
      <div onclick="sidebar.open('notifications', <?php echo $user->id; ?>)" class="ist-notificacoes" title="Notificações">
        <img src="../img/notifications.png" height=48>
      </div>
    </div>
    <main>
      <div class="ist-barra-lateral">
        <div class="ist-barra-cabecalho">
          <div onclick="sidebar.close()" class="ist-botao-acao"><img src="../img/close.png" width=20></div>
          <div onclick="sidebar.expand()" class="ist-botao-acao right"><img src="../img/expand.png" width=20></div>
          <div class="clear"></div>
        </div>
        <div class="ist-barra-conteudo">
        </div>
      </div>
      <div class="ist-visualizacao">
        <div class="ist-center">
          <div class="ist-avatar">
            <img src="../img/istoría-logo-1.png">
          </div>

          <div class="ist-resultados">
          </div>
        </div>
      </div>
    </main>

    <!-- Templates -->
    <div id="dados-vote">
      <div class="info_name"></div>
      <div class="ist-center-barra">
        <div class="info_content"></div>

        <h3>
          <img src="../img/related.png" width=20>
        </h3>

        <div class="relacionados">
        </div>
      </div>

      <div onclick="sidebar.close()" class="ist-botao-acao"><img src="../img/deny.png" width=20></div>
      <div onclick="voteUp(sidebar.id)" class="ist-botao-acao right"><img src="../img/plane.png" width=20></div>
      <div class="clear"></div>
    </div>

    <div id="dados-notification">
      <div class="notifications">
      </div>
    </div>

    <div id="tema-pesquisa">
      <div class="ist-resultado">
        <span class="ist-edit-id">E#_id_</span> <span class="ist-edit-user-name">_user.name_</span> <span class="ist-edit-id">_votes_/_total_</span> _name_
        <img onclick="sidebar.id = _id_;sidebar.open('vote', _id_)" class="ist-visualizar" src="../img/eye.png" width=20>
        <div class="clear"></div>
      </div>
    </div>

    <div id="tema-notfound-pesquisa">
      <div class="center">
        Nenhum resultado encontrado!
      </div>
    </div>

    <script>
      $(window).load(function () {
        sidebar.addProfile("vote", $("#dados-vote").html());
        sidebar.addProfileAction ("vote", loadVotingInformation);
        sidebar.addProfile("notifications", $("#dados-notification").html());
        sidebar.addProfileAction ("notifications", loadNotifications);
        $(".ist-visualizacao").click(function () {
          if (sidebar.isOpen()) {
            sidebar.close();
          }
        });
      });
    </script>
  </body>
</html>
