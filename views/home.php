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
    <script src="../js/search.js"></script>
    <script src="../js/related.js"></script>
    <script src="../js/notifications.js"></script>
    <script src="../js/upload.js"></script>
  </head>

  <body>
    <div class="ist-barra">
      <div onclick="sidebar.open('add')" class="ist-adiciona" title="Adicionar informação">
        <img src="../img/add.png" height=48>
      </div>
      <div class="ist-vota" title="Votar informações">
        <a href="vote.php"><img src="../img/vote.png" height=48></a>
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
          <a href="graph.php">
            <div class="ist-avatar">
              <img src="../img/istoría-logo-1.png">
            </div>
          </a>

          <input class="ist-searchbar sem-foco" type="text" value="pesquisar informações">

          <div class="ist-resultados">
          </div>
        </div>
      </div>
    </main>

    <!-- Templates -->
    <div id="dados-edit">
      <input class="info_name_edit" type="text">
      <textarea class="info_content_edit"></textarea>

      <div onclick="" class="ist-botao-acao right"><img src="../img/magnifier.png" width=20></div>
      <div class="clear"></div>

      <h3>
        <img src="../img/related.png" width=20>
      </h3>

      <div style="position: relative;">
        <input class="info-search-related sem-foco" type="text" value="pesquisar informações">
        <div class="info-search-related-result"></div>
      </div>

      <div class="relacionados">
      </div>

      <div onclick="sendInfo(false)" class="ist-botao-acao"><img src="../img/save.png" width=20></div>
      <div onclick="sendInfo(true)" class="ist-botao-acao right"><img src="../img/plane.png" width=20></div>
      <div class="clear"></div>
    </div>

    <div id="dados-notification">
      <div class="notifications">
      </div>
    </div>

    <div id="tema-pesquisa">
      <div class="ist-resultado">
        <div class="content">
          <b>_name_</b> _badge_
        </div>
        <span onclick="sidebar.open('edit', _id_)" class="ist-botao-edit right">Editar</span>
        <div class="clear"></div>
      </div>
    </div>

    <div id="tema-pesquisa-sidebar">
      <div onclick="related_search.addFirst(_id_, '_name_')" class="ist-resultado-sidebar">
        _name_
      </div>
    </div>

    <div id="badge">
      <span class="ist-revisao">edição publicada aguardando revisão (E#_id_)</span>
    </div>

    <div id="badge_local">
      <span class="ist-publicacao">edição não publicada (E#_id_)</span>
    </div>

    <div id="tema-notfound-pesquisa">
      <div class="center erro">
        Nenhum resultado encontrado!
      </div>
    </div>

    <script>
      $(window).load(function () {
        sidebar.addProfile("edit", $("#dados-edit").html());
        sidebar.addProfileAction ("edit", loadEditingInformation);
        sidebar.addProfile("add", $("#dados-edit").html());
        sidebar.addProfileAction ("add", loadAddingInformation);
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
