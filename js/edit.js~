function loadEditingInformation (id, callback) {
  $.ajax({url: '../json/info/get.php?id='+id+'&markdown'}).done(function (data) {
		var jdata = JSON.parse(data);

		if (jdata.error) {
		}	else {
			$(".info_name_edit").val(jdata.message.name);
			$(".info_content_edit").val(jdata.message.content);

      related_search = new SearchRelated($(".info-search-related"), $(".info-search-related-result"), $(".relacionados"));
      related_search.theme = $("#tema-pesquisa-sidebar").html();
      related_search.notFoundTheme = $("#tema-notfound-pesquisa").html();
      related_search.badge = $("#badge").html();
      related_search.id = Math.floor(jdata.message.id);

			$.ajax({url: '../json/info/related.php?id='+id+'&markdown'}).done(function (data) {
				var jdata = JSON.parse(data);

				if (jdata.error) {
				}	else {
          related_search.active = [];
					$(".relacionados").html('');
					for (var d in jdata.message) {
						var el = $("<div class='relacionado' onclick='related_search.remove(this, "+jdata.message[d].id+")'></div>");
            el.text(jdata.message[d].name);
						$(".relacionados").append(el);
						related_search.active.push(Math.floor(jdata.message[d].id));
					}
				}
			});

      callback();
    }
	});
}

function loadAddingInformation (id, callback) {
	$(".info_name_edit").val('');
	$(".info_content_edit").val('');

  related_search = new SearchRelated($(".info-search-related"), $(".info-search-related-result"), $(".relacionados"));
  related_search.theme = $("#tema-pesquisa-sidebar").html();
  related_search.notFoundTheme = $("#tema-notfound-pesquisa").html();
  related_search.badge = $("#badge").html();
  related_search.id = -1;

  related_search.active = [];
	$(".relacionados").html('');

  callback();
}

function sendInfo (save) {
  // Verifica se ainda tem alguma imagem sendo enviada
  if ($(".progresso").get().length > 0)
    return;

  var title = encodeURIComponent($(".info_name_edit").val());
  var content = encodeURIComponent($(".info_content_edit").val().replace(/\"/g,'\\"'));
  var related = encodeURIComponent(JSON.stringify(related_search.changed));
  var published = save?1:0;

  $.ajax({url: '../json/info/add.php?name='+title+'&published='+published+'&content='+content+'&related='+related+(related_search.id>0?'&id='+related_search.id:'')}).done(function (data) {
    var jdata = JSON.parse(data);

    // Erro ao adicionar
    if (jdata.error) {
    }
    // Sucesso!
    else {
      sidebar.close();
    }
  });
}
