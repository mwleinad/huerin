Event.observe(window, 'load', function() 
{
	if($('rfc'))
	{
		Event.observe($('rfc'), "keyup", function(){ 
			SuggestUser(); 
			//FillDatosFacturacion();
		});
	}

	AddSuggestListener = function(e) {
		var el = e.element();
		var del = el.hasClassName('suggestUserDiv');
		var id = el.identify();
		if(del == true){
			FillRFC(1, id);
			return;
		}

		del = el.hasClassName('closeSuggestUserDiv');
		if(del == true){
			$('suggestionDiv').hide();
			return;
		}
		
	}

	if($('divForm')!= undefined)
	{
		$('divForm').observe("click", AddSuggestListener);
	}
		
});

function FillDatos(id)
{
	$('loadingDivDatosFactura').innerHTML = '<img src="'+WEB_ROOT+'/images/load.gif" />';

//	$('suggestionDiv').hide();
	new Ajax.Request(WEB_ROOT+'/ajax/fill_form_servicios.php', 
	{
  	parameters: {value: id, type: "datos"},
		method:'post',
    onSuccess: function(transport){
      var response = transport.responseText || "no response text";
			var splitResponse = response.split("{#}");
			if(response!="no response text")
			 $('rfc').value = splitResponse[0];
			else
			 $('rfc').value = "";
			
			$('cuenta').value = id;
			$('loadingDivDatosFactura').innerHTML = '';
			if(response!="no response text")
			BuscarServiciosActivos();
		},
    onFailure: function(){ alert('Something went wrong...') }
  });
}

function FillRFC(elem, id)
{
	$('suggestionDiv').hide();
	FillDatos(id);
}

function SuggestUser()
{
	new Ajax.Request(WEB_ROOT+'/ajax/suggest_customer.php', 
	{
  	parameters: {value: $('rfc').value},
		method:'post',
    onSuccess: function(transport){
      var response = transport.responseText || "no response text";
			$('suggestionDiv').show();
			$('suggestionDiv').innerHTML = response;
			AddSuggestListener();
		},
    onFailure: function(){ alert('Something went wrong...') }
  });
}

function GoToWorkflow(id)
{
	window.location = WEB_ROOT+"/workflow/id/"+$('instanciaServicio'+id).value;
}

function UpdateCuentas()
{
	new Ajax.Request(WEB_ROOT + '/ajax/services.php',
   {
	  method : 'post',
	  parameters : 'type=updateCuentas&customerId=' + $('cliente').value,
	  onSuccess : function(transporta)
	  {
		  var respuesta = transporta.responseText;
			$('updateCuentas').innerHTML = respuesta
	  },
	  onFailure : function()
				  {
					  alert('Se detecto un problema con el servidor');
				  }
   });
}

function BuscarServiciosActivos()
{
	if($('rfc').value == "")
	{
		$('cuenta').value = 0;		
	}
	 
	new Ajax.Request(WEB_ROOT + '/ajax/services.php',
   {
	  method : 'post',
	  parameters : 'type=buscarServiciosActivos&customerId=' + $('cliente').value+'&contractId=' + $('cuenta').value+'&rfc=' + $('rfc').value+'&departamentoId=' + $('departamentoId').value+'&responsableCuenta=' + $('responsableCuenta').value+'&deep=' + $('deep').value,
	  onLoading: function(){
			$("loading").style.display = "block";
			$('contenido').innerHTML = "";
		},
	  onSuccess : function(transporta)
	  {
		  $("loading").style.display = "none";
		  var respuesta = transporta.responseText;
			var respuestaSplit = respuesta.split("[#]");
			$('busquedaServicios').innerHTML = respuestaSplit[0];
			$('contenido').innerHTML = respuestaSplit[1];
	  },
	  onFailure : function()
				  {
					  alert('Se detecto un problema con el servidor');
				  }
   });
}

function hideMessage(){
	$("success").style.display = "none";	
}
