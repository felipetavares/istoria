function Search (entry, result) {
  this.entry = entry;
  this.result = result;
  this.list = [];
  this.pointer = 0;
  this.interval = -1;
  this.lastPage = 0;
  this.lastTerm = '';
  this.reloadTime = 1000;

  this.load = function (page, term) {
    var _this = this;
    var search_term = encodeURIComponent(this.entry.val());

    if (page === undefined) {
      page = this.lastPage;
    } else {
      this.lastPage = page;
    }

    if (page !== undefined && this.lastTerm != '') {
      if (!term) {
        term = this.lastTerm;
      }
    }

    if (term !== undefined) {
      this.lastTerm = term;
      search_term = encodeURIComponent(term);
    }

    $.ajax({url: '../json/info/search.php?n=50&s='+search_term+'&p='+page}).done(function (data) {
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
          var dados = _this.theme.replace('_name_', jdata.message[d].name);

          if (jdata.message[d].edit) {
            var badge = _this.badge.replace('_id_', jdata.message[d].edit.id);
            var badge_local = _this.badge_local.replace('_id_', jdata.message[d].edit.id);
            dados = dados.replace('_badge_', Math.floor(jdata.message[d].edit.published)?badge:badge_local);
          } else {
            dados = dados.replace('_badge_', '');
          }

          dados = dados.replace('_id_', jdata.message[d].id);

          var resultado = $(dados);

          _this.result.append(resultado);

          _this.list.push(jdata.message[d]);
        }

        if (jdata.more) {
          _this.result.append($("<div class='ist-resultado-mais' onclick='main_search.load("+(page+1)+", \""+decodeURIComponent(search_term)+"\")'>Página seguinte</div>"));
        }

        if (page > 0) {
          _this.result.append($("<div class='ist-resultado-mais' onclick='main_search.load("+(page-1)+", \""+decodeURIComponent(search_term)+"\")'>Página anterior</div>"));
        }
      }
    });
  }

  var _this = this;

  // @construct
  this.entry.keyup (function (evt) {
    if (evt.keyCode == 13) {
      if (_this.list.length > 0)
        sidebar.open('edit', _this.list[0].id);
    } else {
      _this.load(0, _this.entry.val());
    }
  });

  this.entry.blur (function () {
    _this.entry.val('pesquisar informações');
    _this.entry.addClass('sem-foco');
    _this.interval = setInterval (html5.hitch(_this.load, _this), _this.reloadTime);
  });

  this.entry.focus (function () {
    _this.entry.val('');
    _this.entry.removeClass('sem-foco');
    clearInterval (_this.interval);
    _this.lastTerm = '';
  });
}

$(window).load (function () {
  main_search = new Search($(".ist-searchbar"), $(".ist-resultados"));
  main_search.theme = $("#tema-pesquisa").html();
  main_search.notFoundTheme = $("#tema-notfound-pesquisa").html();
  main_search.badge = $("#badge").html();
  main_search.badge_local = $("#badge_local").html();

  main_search.load();
});
