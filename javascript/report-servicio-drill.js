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

function GoToWorkflow(path, id)
{
	if($('responsableCuenta'))
	{
		var responsableCuenta = $('responsableCuenta').value;
	}
	else
	{
		var responsableCuenta = 0;
	}
	new Ajax.Request(WEB_ROOT+'/ajax/report-obligaciones.php', 
	{
  	parameters: {value: id, type: "goToWorkflow", path: path, rfc: $('rfc').value, responsableCuenta: responsableCuenta, year: $('year').value, from: "report-servicios"},
		method:'post',
    onSuccess: function(transport){
      var response = transport.responseText || "no response text";
			window.open(WEB_ROOT+"/workflow/id/"+id, '_blank');
			//window.location = 
		},
    onFailure: function(){ alert('Something went wrong...') }
  });
}

function FillDatos(id)
{
	$('loadingDivDatosFactura').innerHTML = '<img src="'+WEB_ROOT+'/images/load.gif" />';
//	$('suggestionDiv').hide();
	new Ajax.Request(WEB_ROOT+'/ajax/fill_form_report.php', 
	{
  	parameters: {value: id, type: "datos"},
		method:'post',
    onSuccess: function(transport){
      var response = transport.responseText || "no response text";
			var splitResponse = response.split("{#}");
			$('rfc').value = splitResponse[0];
			$('cliente').value = id;
			$('loadingDivDatosFactura').innerHTML = '';
			//BuscarServiciosActivos();
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
	new Ajax.Request(WEB_ROOT+'/ajax/suggest_report.php', 
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

function ShowClienteTable(id)
{
	if(!$('cliente-'+id).visible())
	{
		$('cliente-'+id).show();
		$('showCliente-'+id).innerHTML = "[-]";
	}
	else
	{
		$('cliente-'+id).hide();
		$('showCliente-'+id).innerHTML = "[+]";
	}
}

function ShowContractTable(id)
{
	if(!$('contract-'+id).visible())
	{
		$('contract-'+id).show();
		$('showContract-'+id).innerHTML = "[-]";
	}
	else
	{
		$('contract-'+id).hide();
		$('showContract-'+id).innerHTML = "[+]";
	}
}

function doSearch(){
	
	$('type').value = "searchNivelUno";
	
	new Ajax.Request(WEB_ROOT+'/ajax/report-servicio.php',
	{
		method:'post',
		parameters: $('frmSearch').serialize(true),
		onLoading: function(){
			$("loading").style.display = "block";
			$('contenido').innerHTML = "";
            $('contenido2').innerHTML = "";
		},
		onSuccess: function(transport){					
			var response = transport.responseText || "Ocurrio un error durante la conexion al servidor. Por Favor Trate de Nuevo";
			var splitResponse = response.split("[#]");
			
			$("loading").style.display = "none";
            $("msg-advertencia").style.display = "none";
			$('contenido').innerHTML = response;
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
	
}

function showGraph(){

	$('type').value = "graph";
	new Ajax.Request(WEB_ROOT+'/ajax/report-obligaciones.php',
	{
		method:'post',
		parameters: $('frmSearch').serialize(true),
		onLoading: function(){
			$("loading").style.display = "block";
			$('contenido').innerHTML = "";
		},
		onSuccess: function(transport){
			
			$("loading").style.display = "none";
			
			var response = transport.responseText || "no response text";

			var splitResponse = response.split("[#]");
//			if(splitResponse[0] == "ok")			
//			{				
				$('contenido').innerHTML = response;
//			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
	
}

function sendEmail(id){
	
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/report-obligaciones.php',
	{
		method:'post',
		parameters: {type: "getEmail"},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('btnSendEmail'), "click", sendMessage);
			Event.observe($('fviewclose'), "click", function(){ sendEmail(0); });
		},
		onFailure: function(){ alert('Something went wrong...') }
	});

}

function sendMessage(){

	$("type").value = "sendEmail";
	correo = $("e_mail").value;
	$("correo").value = correo;
	mensaje = $("mensaje").value;
	$("texto").value = mensaje;
	
	new Ajax.Request(WEB_ROOT+'/ajax/report-obligaciones.php',
	{
		method:'post',
		parameters: $('frmSearch').serialize(true),
		onLoading: function(){
			$("loading").style.display = "block";
		},
		onSuccess: function(transport){
			
			$("loading").style.display = "none";
			
			var response = transport.responseText || "no response text";

			var splitResponse = response.split("[#]");
			if(splitResponse[0] == "ok")			
			{				
				doSendEmail(splitResponse[1], correo, mensaje);
				sendEmail(0);
			}else{
				ShowStatusPopUp(splitResponse[1]);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});

}

function doSendEmail(message, correo, mensaje){
			
	new Ajax.Request(WEB_ROOT+'/ajax/report-obligaciones.php',
	{
		method:'post',
		parameters: {type: "doSendEmail", msg:message, email:correo, msj:mensaje},
		onLoading: function(){
			$("loading").style.display = "block";
		},
		onSuccess: function(transport){
			
			$("loading").style.display = "none";
			
			var response = transport.responseText || "no response text";

			var splitResponse = response.split("[#]");
			if(splitResponse[0] == "ok")			
			{				
				ShowStatusPopUp(splitResponse[1]);
				grayOut(false);
			}else{
				alert("Ocurrio un error al enviar el correo");	
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
	
}

function ModifyComment(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/report-servicio.php',
			{
				method:'post',
				parameters: {type: "editComentario", servicioId:id},
				onSuccess: function(transport){
					var response = transport.responseText || "no response text";
					FViewOffSet(response);
					Event.observe($('closePopUpDiv'), "click", function(){ ModifyComment(0); });
				},
				onFailure: function(){ alert('Something went wrong...') }
			});
}

function SaveEditComentario(id)
{
	new Ajax.Request(WEB_ROOT+'/ajax/report-servicio.php',
			{
				method:'post',
				parameters: $('editComentarioForm').serialize(true),
				onSuccess: function(transport){
					var response = transport.responseText || "no response text";
					var splitResponse = response.split("[#]");
					ShowStatusPopUp(splitResponse[1]);
					console.log(splitResponse[2]);
					$('comentario-'+id).innerHTML = splitResponse[2];
				},
				onFailure: function(){ alert('Something went wrong...') }
			});
}
function showLevel(id){
	$(id).toggle();
}
function ToggleTask(id)
{
    $$('.tasks').each(
        function (e) {
            e.setStyle({display:'none'});
        }
    );
    $('step-'+id).show();
}

function ShowSixLevel(id){

    if($('responsableCuenta'))
    {
        var responsableCuenta = $('responsableCuenta').value;
    }
    else
    {
        var responsableCuenta = 0;
    }
    new Ajax.Request(WEB_ROOT+'/ajax/report-obligaciones.php',
        {
            method:'post',
            parameters: {type: "showSixLevel", id:id,rfc:$('rfc').value, responsableCuenta: responsableCuenta, year: $('year').value, from: "report-servicios"},
            onLoading: function(){
                $("contenido2").innerHTML = "";
            },
			onSuccess: function(transport){
                var response = transport.responseText || "no response text";
                var splitResponse = response.split("[#]");
                console.log(splitResponse[1]);
                $('ul-six-level-'+id).innerHTML = splitResponse[1];

               $('contenido2').innerHTML = splitResponse[2];
            },
            onFailure: function(){ alert('Something went wrong...') }
        });
    $('ul-six-level-'+id).toggle();
}