<?php
  session_start();
  include '../db/db.php';
  include '../php/Parsedown.php';
  $Parsedown = new Parsedown();

  $user = null;

  if (isset($_SESSION['user_id']))
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
      @import url(../css/main.css);
    </style>
    <script src="../js/html5.js"></script>
    <script src="../js/jquery.js"></script>
  </head>

  <body>
    <div id="ist-barra">
      <h1>ιστορία</h1>
    </div>

    <div class="clear"></div>

    <main>
      <div class="links">
        <a href="graph.php?id=<?php echo $id; ?>">
          <div class="ist-botao">
            Faça um tour
          </div>
        </a>
      </div>

      <h2>Manual de uso</h2>

      <p>
        Está com dúvidas em relação a algo? Leia o nosso tutorial para auxiliar a sua melhor experiência com o nosso sistema!
      </p>

      <blockquote>
        <img class="warning" src="../img/warning.png">
        Você está com alguma dúvida que não está presente nesta página? Entre em contato, e responderemos o mais rápido possível.
        <div class="clear"></div>
      </blockquote>

      <h2>Editor de texto</h2>

      <p>
        Após fazer login você verá a sessão de fatos históricos, assim que você clicar verá os fatos já criado por você ou você poderá criar um novo fato. Ao fazer isso aparecerá a tela do editor de texto. O editor se utiliza da linguagem de marcação simples Markdown e para fazer um texto bonito e interessante você tem que digitar com a seguinte formatação:
      </p>

      <p>
        <ul>
          <li> Escrever palavras em negrito:</li>
        </ul>
          <pre><b>**negrito**</b></pre>
        <ul>
          <li> Escrever palavras em itálico:</li>
        </ul>
          <pre><em>*itálico*</em></pre>
        <ul>
          <li>Lista com marcador:</li>
        </ul>

          <pre>
* Colocando um espaço depois do *
* Exemplo
          </pre>
        <ul>
          <li>Lista numerada:</li>
        </ul>
          <pre>
1. Item 1
2. Item 2
          </pre>

          <blockquote>
            <img class="warning" src="../img/warning.png">
            Mesmo que você coloque a numeração incorreta, o editor corrige!
            <div class="clear"></div>
          </blockquote>

          <ul>
            <li>Cabeçalho: É definido pela quantidade de #, depois do # sempre coloque o espaço</li>
          </ul>
          <pre>
# Nível 1
## Nível 2
### Nível 3
#### Nível 4...
          </pre>

          <ul>
            <li>Citações:</li>
          </ul>

          <pre>
> As citações devem ficar assim
> Todas as linhas
          </pre>

          <ul>
            <li>Código embutido (acento agudo):</li>
          </ul>

          <pre>
Exemplo de `código embutido`
          </pre>

          <ul>
            <li>Blocos de código:</li>
          </ul>

          <pre>
```
Exemplo de um bloco de código.
```
          </pre>

          <ul>
            <li>Links:</li>
          </ul>

          <pre>
[Texto do link](http://exemplodeurl.com)
          </pre>

        </p>
    </main>
  <script>
    $(window).load(function () {
      $(".ajuda>img").click(function () {
        var copy = $(this).clone();
        copy.addClass("overlay");

        $(".ist-visualizacao").append(copy);

        copy.css("position", "fixed");
        copy.css("width", "90%");
        copy.css("top", "50%");
        copy.css("margin-top", (-copy.height()/2)+"px");
        copy.css("left", "5%");
        copy.css("left", "5%");
        copy.css("box-shadow", "0 0 6px 0 white");
        copy.css("border-radius", "6px");
      });

      $(document).on('click', '.overlay', function () {
        $(this).remove();
      });
    });
  </script>
  </body>
</html>
