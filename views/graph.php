<!doctype HTML>

<html>
  <head>
    <title>ιστορία</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="mobile-web-app-capable" content="yes">
    <style>
      /* css principal */
      @import url(../css/home.css);
      @import url(../css/canvas.css);
    </style>
    <script src="../js/html5.js"></script>
    <script src="../js/jquery.js"></script>
    <script src="../js/jquery-ui.js"></script>
    <script src="../js/sidebar.js"></script>
    <script src="../js/related.js"></script>

    <script src="../js/gl-matrix.js"></script>
    <script src="../js/webgl.js"></script>
    <script src="../js/show.js"></script>
    <script src="../js/viewer-graph.js"></script>
    <script src="../conversor/esfera.js"></script>

    <!-- Shader de vértices padrão para o WebGL -->
    <script id="v-default" type="shader/vertex">
      // Posição do vértice
      attribute vec3 vertexPosition;

      // Matriz de Projeção e ModelView
      uniform mat4 pMatrix;
      uniform mat4 mvMatrix;
      uniform mat4 nMatrix;
      uniform vec4 color;

      varying vec4 vn;

      // Encontra a posição final do vértice (multiplica as matrizes pela posição 3d do vértice)
      void main(void) {
      	gl_Position = pMatrix * mvMatrix * vec4(vertexPosition, 1);

        vn = color;
      }
    </script>
    <!-- Shader de fragmento padrão para o WebGL -->
    <script id="f-default" type="shader/fragment">
      // Especifica a precisão do tipo float
      precision mediump float;

      varying vec4 vn;

      // Cor do pixel (fragmento)
      // As cores são RGBA normalizadas [0,1]
      void main(void) {
      	gl_FragColor = vn;
      }
    </script>
    <!-- Gradiente  -->
    <script id="v-gradient" type="shader/vertex">
      // Posição do vértice
      attribute vec3 vertexPosition;

      // Matriz de Projeção e ModelView
      uniform mat4 pMatrix;
      uniform mat4 mvMatrix;
      uniform mat4 nMatrix;
      uniform vec4 color;

      varying vec4 vn;

      vec4 start = vec4(0.8,0.8,0.8,1);
      vec4 end = vec4(0.3,0.3,0.3,1);

      // Encontra a posição final do vértice (multiplica as matrizes pela posição 3d do vértice)
      void main(void) {
      	gl_Position = vec4(vertexPosition, 1);

        float q = vertexPosition.y/2.0;

        vn = start*q+end*(1.0-q);
      }
    </script>
    <script id="f-gradient" type="shader/fragment">
      // Especifica a precisão do tipo float
      precision mediump float;

      varying vec4 vn;

      // Cor do pixel (fragmento)
      // As cores são RGBA normalizadas [0,1]
      void main(void) {
      	gl_FragColor = vn;
      }
    </script>
    <script>
      <?php
        if (isset($_GET['id']))
          echo "istoriaLoadInfoID = ${_GET['id']};";
      ?>
    </script>
  </head>

  <body>
    <div class="ist-barra">
      <?php
        session_start();
        if (isset($_SESSION['user_id'])) {
          echo "<div class='ist-logout' title='Sair'>
            <a href='logout.php'><img src='../img/logout.png' height=48></a></div>";
          echo "
            <div class=\"ist-notificacoes\" title=\"Home\">
              <a href=\"home.php\"><img src=\"../img/home.png\" height=48></a>
            </div>";
        }
      ?>
      <div class="info-search-main">
        <div>
          <input class="info-search sem-foco" type="text" value="pesquisar informações">
        </div>
        <div class="info-search-result"></div>
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
        <canvas id="canvas"></canvas>
        <div class="nada-carregado">Nenhuma informação carregada</div>
      </div>
    </main>

    <!-- Templates -->
    <div id="dados-show">
      <div class="info_name"></div>
      <div class="ist-center-barra">
        <div class="info_content"></div>
      </div>
    </div>

    <div id="tema-pesquisa-sidebar">
      <div onclick="related_search.addFirst(_id_, '_name_')" class="ist-resultado-sidebar">
        _name_
      </div>
    </div>

    <div id="badge">
      <span class="ist-revisao">edição em revisão</span>
    </div>

    <div id="tema-notfound-pesquisa">
      <div class="ist-resultado center">
        Nenhum resultado encontrado!
      </div>
    </div>

    <script>
      $(window).load(function () {
        sidebar.addProfile("show", $("#dados-show").html());
        sidebar.addProfileAction ("show", showInformation);

        related_search = new SearchRelated($(".info-search"), $(".info-search-result"), null);
        related_search.theme = $("#tema-pesquisa-sidebar").html();
        related_search.notFoundTheme = $("#tema-notfound-pesquisa").html();
        related_search.badge = $("#badge").html();
        related_search.id = -1;
      });
    </script>
  </body>
</html>
