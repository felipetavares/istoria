$(document).ready (function () {
  $("#ist-login").click (function () {
    if ($("#entrar-dialogo").get(0).open)
      $("#entrar-dialogo").fadeOut(300);
    else
      $("#entrar-dialogo").fadeIn(300);

    $("#entrar-dialogo").get(0).open = !$("#entrar-dialogo").get(0).open;
  });
});
