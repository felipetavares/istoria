$(window).load (function () {
  loadVotes();
  setInterval (loadVotes, 10000);
});

function loadVotes () {
  var page = 0;
  var _this = {};
  _this.result = $(".ist-resultados");
  _this.theme = $("#tema-pesquisa").html();

  $.ajax({url: '../json/edit/list.php?n=50'+'&p='+page}).done(function (data) {
    console.log (data);

    var jdata = JSON.parse(data);

    // Erro ao ler dados
    if (jdata.error) {
      _this.result.html(_this.notFoundTheme);
      _this.list = [];
      _this.pointer = 0;
    } else {
      _this.result.html('');
      _this.list = [];
      _this.pointer = 0;

      for (var d in jdata.message) {
        if (Math.floor(jdata.message[d].published)) {
          var dados = _this.theme.replace(/_name_/g, jdata.message[d].name);
          dados = dados.replace(/_id_/g, jdata.message[d].id);
          dados = dados.replace(/_user\.name_/g, jdata.message[d].user.name);
          dados = dados.replace(/_total_/g, jdata.message[d].total);
          dados = dados.replace(/_votes_/g, jdata.message[d].votes);

          var resultado = $(dados);

          _this.result.append(resultado);

          _this.list.push(jdata.message[d]);
        }
      }

      if (jdata.more) {
        _this.result.append($("<div class=/ist-resultado-mais' onclick='vote.load("+(page+1)+", \""+decodeURIComponent(search_term)+"\")/g>Página seguinte</div>"));
      }

      if (page > 0) {
        _this.result.append($("<div class=/ist-resultado-mais' onclick='vote.load("+(page-1)+", \""+decodeURIComponent(search_term)+"\")/g>Página anterior</div>"));
      }
    }
  });
}

function loadVotingInformation (id, callback) {
  $.ajax({url: '../json/edit/get.php?'+'&id='+id}).done(function (data) {
    var jdata = JSON.parse(data);

    // Erro ao ler dados
    if (jdata.error) {
    } else {
      var edit = jdata.message;
      $(".info_name").html(edit.name);
      $(".info_content").html(edit.diff);

      $.ajax({url: '../json/edit/related.php?simulate&'+'id='+id}).done(function (data) {
        var jdata = JSON.parse(data);

        if (jdata.error) {
        } else {
          $(".relacionados").html('');
          for (var i in jdata.message) {
            var el = $("<div class='relacionado'></div>");

            if (jdata.message[i].added) {
              el.addClass('adicionado');
            } else if (jdata.message[i].removed) {
              el.addClass('removido');
            }

            el.text(jdata.message[i].name);
            $(".relacionados").append(el);
          }
        }

        callback();
      });
    }
  });
}

function voteUp (id) {
  $.ajax({url: 'vote_up.php?id='+id}).done(function (data) {
    loadVotes();
    sidebar.close();
  });
}
