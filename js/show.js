function showInformation (id, callback) {
  $.ajax({url: '../json/info/get.php?id='+id}).done(function (data) {
		var jdata = JSON.parse(data);

		if (jdata.error) {
		}	else {
			$(".info_name").html(jdata.message.name);
			$(".info_content").html(jdata.message.content);

      //net.loadInfo(id);

      callback();
    }
  });
}
