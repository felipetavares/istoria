function SearchRelated (entry, result, activeDOM) {
  this.entry = entry;
  this.result = result;
  this.list = [];
  this.active = [];
  this.changed = [];
  this.activeDOM = activeDOM;
  this.pointer = 0;
  this.baseUrl = "../";

  this.pushChanged = function (id) {
    id = Math.floor(id);
    var index;

    if ((index = this.changed.indexOf(id)) < 0) {
      this.changed.push(id);
    } else {
      this.changed.splice(index, 1);
    }
  }

  this.remove = function (element, id) {
    $(element).remove();
    var i;
    if ((i = this.active.indexOf(Math.floor(id))) >= 0) {
      this.active.splice (i, 1);
      this.pushChanged(id);
    }
  }

  this.addFirst = function (id, name) {
    if (!this.activeDOM) {
      this.show(id, name);
      return;
    }

    if (id !== undefined && name !== undefined) {
      if (this.list.length > 0) {
        if (this.active.indexOf(Math.floor(id)) < 0 &&
            this.id != Math.floor(id)) {
          this.active.push(Math.floor(id));
          this.pushChanged(id);
          var el = $("<div class='relacionado' onclick='related_search.remove(this, "+id+")'></div>");
          el.text(name);
          this.activeDOM.append(el);

          this.result.html('');
          this.entry.val('');
          this.result.fadeOut();
        }
      }
    } else {
      if (this.list.length > 0) {
        if (this.active.indexOf(Math.floor(this.list[this.pointer].id)) < 0 &&
            this.id != Math.floor(this.list[this.pointer].id)) {
          this.pushChanged(this.list[this.pointer].id);
          this.active.push(Math.floor(this.list[this.pointer].id));
          var el = $("<div class='relacionado' onclick='related_search.remove(this, "+this.list[this.pointer].id+")'></div>");
          el.text(this.list[this.pointer].name);
          this.activeDOM.append(el);

          this.result.html('');
          this.entry.val('');
          this.result.fadeOut();
        }
      }
    }
  }

  this.load = function (page, term) {
    var _this = this;
    var search_term = encodeURIComponent(this.entry.val());

    if (!page)
      page = 0;

    if (term) {
      search_term = encodeURIComponent(term);
    }

    $.ajax({url: _this.baseUrl+'json/info/search.php?s='+search_term+'&p='+page}).done(function (data) {
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
          var dados = _this.theme.replace(/_name_/g, jdata.message[d].name);
          dados = dados.replace(/_badge_/g, jdata.message[d].edit?this.badge:'');
          dados = dados.replace(/_id_/g, jdata.message[d].id);

          var resultado = $(dados);

          _this.result.append(resultado);

          jdata.message[d].dom = resultado;

          _this.list.push(jdata.message[d]);
        }

        if (_this.list.length > 0)
          _this.result.children().eq(_this.pointer).addClass('selecionado');

        if (jdata.more) {
          _this.result.append($("<div class='ist-resultado-mais' onclick='related_search.load("+(page+1)+", \""+decodeURIComponent(search_term)+"\")'>Página seguinte</div>"));
        }

        if (page > 0) {
          _this.result.append($("<div class='ist-resultado-mais' onclick='related_search.load("+(page-1)+", \""+decodeURIComponent(search_term)+"\")'>Página anterior</div>"));
        }

        _this.result.fadeIn();
      }
    });
  }

  this.up = function () {
    if (this.list.length > 0) {
      if (this.pointer > 0) {
        this.result.children().eq(this.pointer).removeClass('selecionado');
        this.pointer--;
        this.result.children().eq(this.pointer).addClass('selecionado');
      }
    }
  }

  this.down = function () {
    if (this.list.length > 0) {
      if (this.pointer < this.list.length-1) {
        this.result.children().eq(this.pointer).removeClass('selecionado');
        this.pointer++;
        this.result.children().eq(this.pointer).addClass('selecionado');
      }
    }
  }

  this.show = function (id, name) {
    if (this.list.length > 0) {
      this.result.html('');
      this.entry.val('');
      this.result.fadeOut();

      if (sidebar.isOpen()) {
        net.loadInfo(id!==undefined?id:this.list[this.pointer].id);
        sidebar.open('show', id!==undefined?id:this.list[this.pointer].id);
      } else {
        net.loadInfo(id!==undefined?id:this.list[this.pointer].id);
      }
    }
  }

  var _this = this;

  // @construct
  this.entry.keydown (function (evt) {
    if (evt.keyCode == 13) {
      _this.addFirst();
    } else if (evt.keyCode == 38) {
      evt.preventDefault();
      _this.up();
    } else if (evt.keyCode == 40) {
      evt.preventDefault();
      _this.down();
    } else {
      _this.load();
    }

    //$(_this.list[_this.pointer].dom).text("Hmm");
    //console.log('hmm');
  });

  this.entry.focus (function () {
    _this.entry.val('');
    _this.entry.removeClass('sem-foco');
  });

  this.entry.blur (function () {
    _this.entry.val('pesquisar informações');
    _this.entry.addClass('sem-foco');
    _this.result.fadeOut();
  });

  this.result.fadeOut();
}
