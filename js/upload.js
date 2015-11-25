function insertText (element, text) {
  element = $(element).get()[0];

  var caretPos = element.selectionStart;
  var cur = $(element).val();
  $(element).val(cur.substring(0, caretPos) + text + cur.substring(caretPos));
}

$(window).load(function () {
  var selector = '.ist-barra-lateral';

  $(selector).on(
      'dragover',
      function(e) {
          e.preventDefault();
          e.stopPropagation();
      }
  );
  $(selector).on(
      'dragenter',
      function(e) {
          e.preventDefault();
          e.stopPropagation();
      }
  );

  $(selector).on(
      'drop',
      function(e){
          if(e.originalEvent.dataTransfer){
              if(e.originalEvent.dataTransfer.files.length) {
                  e.preventDefault();
                  e.stopPropagation();

                  var file = e.originalEvent.dataTransfer.files[0];

                  if (file.type.match(/.*image.*/g)) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                      $.ajax({url: '../json/image/make_id.php'}).done(function (data) {
                        var jdata = JSON.parse(data);

                        var progress = $("<div class='progresso'></div>");
                        var progressIndicator = $("<div class='progresso-indicador'></div>");
                        $(".ist-barra-cabecalho").append(progress);
                        progress.append(progressIndicator);

                        if (jdata.error) {

                        } else {
                          var id = jdata.message;
                          var data = e.target.result;

                          insertText(".info_content_edit", "!["+file.name+"]("+"../db/"+id+")");

                          $.ajax({xhr: function () {
                            var req = new window.XMLHttpRequest();
                            req.upload.addEventListener("progress", function(evt){
                              if (evt.lengthComputable) {
                                var percentComplete = evt.loaded / evt.total;
                                progressIndicator.css("width", (percentComplete*100)+"%");
                                if (percentComplete >= 1) {
                                  progress.fadeOut(function () {
                                    $(this).remove();
                                  });
                                }
                              }
                            }, false);
                            return req;
                          }, type: 'post', url: '../json/image/write.php', data: ({id: id, data: btoa(data)})}).done(function (data) {
                            var jdata = JSON.parse(data);

                            if (jdata.error) {
                            } else {
                              // Sucesso
                            }
                          });
                        }
                      });
                    };

                    reader.readAsBinaryString(file);
                }
              }
          }
      }
  );
});
