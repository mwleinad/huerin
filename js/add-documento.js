var AJAX_PATH = WEB_ROOT + "/ajax/add-documento.php";
jQ(document).on("click", ".addFile", function () {
  var tipo = jQ(this).data("tipo");
  var id = jQ(this).data("id");
  grayOut(true);
  jQ("#fview").show();
  jQ.ajax({
    url: AJAX_PATH,
    data: { type: "openModalFile", contractId: id, tipo: tipo },
    type: "POST",
    success: function (response) {
      var resSplit = response.split("[#]");
      FViewOffSet("");
      FViewOffSet(resSplit[0]);
      customOptions = {
        url: AJAX_PATH,
      };
      createDropzone("#idDropzone", customOptions);
    },
  });
});
jQ(document).on("click", "#closePopUpDiv", function () {
  close_popup();
});
jQ(document).on("change", "#tipoDocumentoId", function () {
  if (this.value == 24) jQ("#tagExpiration").show();
  else jQ("#tagExpiration").hide();
});
function close_popup() {
  $("fview").innerHTML = "";
  $("fview").hide();
  grayOut(false);
  return;
}
//dropzone para actualizar archivos en workflow>pasos>tareas
jQ(document).on("click", ".spanUpdateWorkflow", function () {
  var id = jQ(this).data("id");
  grayOut(true);
  jQ("#fview").show();
  jQ.ajax({
    url: WEB_ROOT + "/ajax/workflow.php",
    data: { type: "openModalUpdateFilesWorkflow", contractId: id },
    type: "POST",
    success: function (response) {
      var resSplit = response.split("[#]");
      FViewOffSet("");
      FViewOffSet(resSplit[0]);
      customOptions = {
        url: WEB_ROOT + "/ajax/workflow.php",
        acceptedFiles: ".zip,.gz",
        dictInvalidFileType: "Archivo no permitido, compruebe la extension.",
        timeout: 180000,
      };
      createDropzone("#idDropzone", customOptions);
    },
  });
});
