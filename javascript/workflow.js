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
          
          window.location = WEB_ROOT+"/servicios";
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



