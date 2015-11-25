function Sidebar (visual, sidebar) {
  this.visual = visual;
  this.sidebar = sidebar;
  this.isopen = false;
  this.expanded = false;
  this.profiles = [];
  this.profileActions = [];

  this.addProfile = function (name, content) {
    this.profiles[name] = content;
  }

  this.addProfileAction = function (name, content) {
    this.profileActions[name] = content;
  }

  this.open = function (profile, profileData) {
    var offset;
    var _this = this;

    this.sidebar.children(".ist-barra-conteudo").html(this.profiles[profile]);

    if (this.profileActions[profile]) {
      this.profileActions[profile](profileData, function () {
        offset = -_this.sidebar.outerWidth();

        _this.visual.animate({left: offset+'px'}, function () {
          _this.isopen = true;
        });
      });
    } else {
      offset = -this.sidebar.outerWidth();

      this.visual.animate({left: offset+'px'}, function () {
        _this.isopen = true;
      });
    }
  }

  this.close = function () {
    var _this = this;

    this.visual.animate({left: 0+'px'}, function () {
      _this.isopen = false;
    });
  }

  this.expand = function () {
    var _this = this;

    if (this.expanded) {
      _this.expanded = false;
      this.visual.animate({left: (-256)+'px'}, function () {
        _this.isopen = true;
      });
      this.sidebar.animate({width: 256+'px',backgroundColor: '#777'});
    } else {
      _this.expanded = true;
      var offset = -this.visual.width();
      this.visual.animate({left: offset+'px'}, function () {
        _this.isopen = true;
      });
      this.sidebar.animate({width: (-offset)+'px', backgroundColor: '#333'});
    }
  }

  this.isOpen = function () {
    return this.isopen;
  }
}

$(window).load (function () {
  sidebar = new Sidebar($(".ist-visualizacao"), $(".ist-barra-lateral"));
});
