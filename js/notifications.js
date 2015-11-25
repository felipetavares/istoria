function loadNotifications (id, callback, archive) {
  $.ajax({url: '../json/notifications/list.php?user='+id}).done(function (data) {
    var jdata = JSON.parse(data);

    if (jdata.error) {
      // No notifications
      if (jdata.message == "0") {
        $(".notifications").html('');

        var notification = $("<div class='notification-erro'>Nenhuma notificação</div>")

        $(".notifications").append(notification);

        callback();
      }
    } else {
      $(".notifications").html('');

      var n = 0;

      for (var d in jdata.message) {
        var notification = $("<div onclick='viewNotification(this, "+jdata.message[d].id+")' class='notification'></div>");
        notification.html(jdata.message[d].content);

        if (jdata.message[d].viewed == '0' && !archive) {
          $(".notifications").append(notification);
          n++;
        } else {
          if (archive) {
            var notification = $("<div class='notification'></div>");
            notification.html(jdata.message[d].content);
            $(".notifications").append(notification);
            n++;
          }
        }
      }

      if (!n) {
        var notification = $("<div class='notification-erro'>Nenhuma notificação</div>")
        $(".notifications").append(notification);
      }

      if (archive) {
        var mais = $("<div onclick='loadNotifications("+id+", null, false)' class='notification-mais'>Não lidas</div>")
        $(".notifications").append(mais);
      } else {
        var mais = $("<div onclick='loadNotifications("+id+", null, true)' class='notification-mais'>Lidas</div>")
        $(".notifications").append(mais);
      }

      if (callback)
        callback();
    }
  });
}

function viewNotification (el, id) {
  $(el).animate({right: "-100%"}, function () {
    $(el).remove();
    $.ajax({url: '../json/notifications/view.php?id='+id}).done(function (data) {
    });
  });
}
