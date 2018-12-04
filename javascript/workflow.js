function ToggleTask(id)
{
    $$('.tasks').each(
         function (e) {
                e.setStyle({display:'none'});
         }
    );
    $('step-'+id).show();
}
function CancelarWorkFlow(id)
{
  var message = "Realmente desea desactivar este workflow?";
  if(!confirm(message))
  {
    return;
  }
  new Ajax.Request(WEB_ROOT+'/ajax/services.php',
  {
    method:'post',
    parameters: {type: "cancelWorkFlow", instanciaServicioId: id},
    onSuccess: function(transport){
      var response = transport.responseText || "no response text";
      var splitResponse = response.split("[#]");
      window.location = WEB_ROOT+"/report-servicio";
    },
    onFailure: function(){ alert('Something went wrong...') }
  });
}

function ReactivarWorkFlow(id)
{
  var message = "Realmente desea activar este workflow?";
  if(!confirm(message))
  {
    return;
  }
  new Ajax.Request(WEB_ROOT+'/ajax/services.php',
  {
    method:'post',
    parameters: {type: "activateWorkFlow", instanciaServicioId: id},
    onSuccess: function(transport){
      var response = transport.responseText || "no response text";
      var splitResponse = response.split("[#]");

      window.location = WEB_ROOT+"/servicios";
    },
    onFailure: function(){ alert('Something went wrong...') }
  });
}
function HideButtons(){

var buttons = document.getElementsByClassName("btnEnviar");
for(i=0; i<buttons.length; i++){
    buttons[i].style.display = "none";
}

}
function UpdateDateWorkflow(el) {
  var doUpdate =  confirm('Se ha modificado la fecha  ¿ Desea guardar los cambios ? ');

  if(!doUpdate)
      return;
  jQ.ajax({
          url: WEB_ROOT+"/ajax/services.php",
          data: jQ('#frmWorkFlow').serialize(true)+'&dateNew='+jQ('#'+el.id).val(),
          type: 'POST',
          beforeSend: function(){
          },
          success: function(response){
              var splitResponse = response.split("[#]");
                  ShowStatusPopUp(splitResponse[1]);

          },
      }

  )
}
var createDropzoneWorkflow = ()=>{
    if(jQ('#frm-workflow').length) {
        jQ('form#frm-workflow').each(
            function() {
                var ext = jQ(this).data('extensiones');
                if(ext!='')
                    var opts =  {acceptedFiles:ext, dictInvalidFileType:"Archivo no permitido, compruebe la extension."}
                else
                    var opts = {};

                var objDrop = dropzoneDefault(this,opts);
                objDrop.on('sending', function (file, xhr, formData) {
                    formData.append('type', 'saveFromWorkflow');
                });
                objDrop.on("maxfilesexceeded", function (file) {
                    this.removeAllFiles();
                    this.addFile(file);
                });
                objDrop.on("complete", function (file) {
                    this.removeFile(file);
                });
                objDrop.on('success', function (file, data) {
                    if(data.message=='ok'){
                        jQ('.tasks').html(data.templateRefresh);
                        //cambiar de color el paso segun las tareas
                        jQ("#step-"+data.stepId).removeClass(data.classRemove);
                        jQ("#step-"+data.stepId).addClass(data.classAdd);
                        ShowStatusPopUp(data.notificacion);
                        createDropzoneWorkflow();
                    }else{
                        ShowStatusPopUp(data.notificacion);
                    }
                });
                objDrop.on("error", function(file, errorMessage) {
                    ShowErrorOnPopup(errorMessage,1)
                });
            }

        );

    }
}
//controlar la visualizacion de los pasos y tareas.
jQ(document).on('click','.boxStep',function () {
    /*var tasks = document.querySelectorAll('.tasks');
    tasks.forEach(
        (e) => {
            jQ(e).removeClass('zoneTaskShow');
            jQ(e).addClass('zoneTaskHiden');
        });*/
    var myForm = document.getElementById('frmWorkFlow');
    var form = new FormData(myForm);
    form.set('type','listTasksStep');
    form.append('stepId',jQ(this).data("id"));
    jQ.ajax({
        url: WEB_ROOT+"/ajax/workflow.php",
        data: form,
        processData: false,
        contentType:false,
        type: 'POST',
        beforeSend: function(){
            jQ('.tasks').removeClass('zoneTaskShow');
            jQ('.tasks').addClass('zoneTaskHiden');
            jQ('.tasks').html('');
        },
        success: function(response){
            jQ('.tasks').removeClass('zoneTaskHiden');
            jQ('.tasks').addClass('zoneTaskShow');
            jQ('.tasks').html(response);
            //se crean los dropzones de las tareas
            createDropzoneWorkflow();

        },
    });
});

jQ(document).on('click','.deleteFileWorkflow',function () {
    var myForm = document.getElementById('frmWorkFlow');
    var form = new FormData(myForm);
    form.set('type','deleteFileTask');
    form.append('taskFileId',jQ(this).data('file'));
    form.append('stepId',jQ(this).data('step'));
    var con = confirm('Esta seguro de eliminar este archivo');
    if(!con)
        return;

    jQ.ajax({
        url: WEB_ROOT+"/ajax/add-documento.php",
        data: form,
        processData: false,
        contentType:false,
        dataType:'json',
        type: 'POST',
        beforeSend: function(){
        },
        success: function(data){
            if(data.message=='ok'){
                jQ('.tasks').html(data.templateRefresh);
                //cambiar de color el paso segun las tareas
                jQ("#step-"+data.stepId).removeClass(data.classRemove);
                jQ("#step-"+data.stepId).addClass(data.classAdd);
                ShowStatusPopUp(data.notificacion);

                createDropzoneWorkflow();
            }else{
                ShowStatusPopUp(data.notificacion);
            }
        },
    });
});



