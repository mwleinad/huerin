Event.observe(window, 'load', function() 
{
	if($('rfc'))
	{
		var time_id =  -1;
		var field_value = '';
		Event.observe($('rfc'), "keyup", function(e){
			field_value =  this.value;
			clearTimeout(time_id);
			if(field_value.length>=2){
				time_id =  setTimeout(function () {
                    SuggestUser();
                },400)
			}
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
	
	$('type').value = "search";
	
	new Ajax.Request(WEB_ROOT+'/ajax/report-servicio.php',
	{
		method:'post',
		parameters: $('frmSearch').serialize(true),
		onLoading: function(){
			$("loading").style.display = "block";
			$('contenido').innerHTML = "";
		},
		onSuccess: function(transport){					
			var response = transport.responseText || "Ocurrio un error durante la conexion al servidor. Por Favor Trate de Nuevo";
			var splitResponse = response.split("[#]");
			
			$("loading").style.display = "none";
			
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
//nuevos eventos con jquery
jQ(document).on('click','.detailCobranza',function (e) {
	e.preventDefault();
	var datos =  jQ(this).data('datos');
	jQ.ajax({
		url:WEB_ROOT+'/ajax/workflow.php',
		type:'post',
		data:{type:'viewFacturas',datos:JSON.stringify(datos)},
		success:function (response) {
            grayOut(true);
            $('fview').show();
            FViewOffSet(response);
        },
        error:function () {
			alert("Error al mostrar informacion!!");

        }
	});
});