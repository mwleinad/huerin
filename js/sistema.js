function VistaPreviaComprobante()
{
	var message = "Esto solo generara una vista previa, para generar un comprobante da click en Generar Comprobante.";
	if(!confirm(message))
  {
		return;
	}

	$('totalesDesglosadosDiv').innerHTML = '<img src="'+WEB_ROOT+'/images/load.gif" />Generando Vista Previa, este proceso puede tardar unos segundos';

	$('nuevaFactura').enable();
	var nuevaFactura = $('nuevaFactura').serialize();
	$('nuevaFactura').disable();
	$('rfc').enable();
	$('userId').enable();
	$('formaDePago').enable();
	$('condicionesDePago').enable();
	$('metodoDePago').enable();
	$('tasaIva').enable();
	$('tiposDeMoneda').enable();
	$('porcentajeRetIva').enable();
	$('porcentajeDescuento').enable();
	$('tipoDeCambio').enable();
	$('porcentajeRetIsr').enable();
	$('tiposComprobanteId').enable();
	$('sucursalId').enable();
	$('porcentajeIEPS').enable();
	$('nuevaFactura').enable();

	if($('reviso')) var reviso = $('reviso').value;
	else var reviso = "";

	if($('autorizo')) var autorizo = $('autorizo').value;
	else var autorizo = "";

	if($('recibio')) var recibio = $('recibio').value;
	else var recibio = "";

	if($('vobo')) var vobo = $('vobo').value;
	else var vobo = "";

	if($('pago')) var pago = $('pago').value;
	else var pago = "";

	new Ajax.Request(WEB_ROOT+'/ajax/sistema.php',
	{
  	parameters: {nuevaFactura: nuevaFactura, observaciones: $('observaciones').value, type: "vistaPreviaComprobante", reviso: reviso, autorizo: autorizo, recibio: recibio, vobo: vobo, pago: pago, pago: pago},
		method:'post',
    onSuccess: function(transport){

      var response = transport.responseText || "no response text";
			var splitResponse = response.split("|");
			if(splitResponse[0] == "fail")
			{
				$('divStatus').innerHTML = splitResponse[1];
				$('centeredDiv').show();
				grayOut(true);
         $('totalesDesglosadosDiv').innerHTML = "";
			}
			else
			{
        $('totalesDesglosadosDiv').innerHTML = response;
			}

		},
    onFailure: function(){ alert('Something went wrong...') }
  });
}


function SuggestUser()
{
	new Ajax.Request(WEB_ROOT+'/ajax/suggest.php',
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

function FillRFC(elem, id)
{
	$('suggestionDiv').hide();
	FillDatosFacturacion(id);
}

function FillNoIdentificacion(elem, id)
{
	$('noIdentificacion').value = id;
	$('suggestionProductDiv').hide();
	FillConceptoData();
}

function FillImpuestoId(elem, id)
{
	$('impuestoId').value = id;
	$('suggestionImpuestoDiv').hide();
	FillImpuestoData();
}

function SuggestProduct()
{
	new Ajax.Request(WEB_ROOT+'/ajax/suggest_x.php',
	{
  	parameters: {value: $('noIdentificacion').value, type: "producto"},
		method:'post',
    onSuccess: function(transport){
      var response = transport.responseText || "no response text";
			$('suggestionProductDiv').show();
			$('suggestionProductDiv').innerHTML = response;
		},
    onFailure: function(){ alert('Something went wrong...') }
  });
}

function SuggestImpuesto()
{
	new Ajax.Request(WEB_ROOT+'/ajax/suggest_x.php',
	{
  	parameters: {value: $('impuestoId').value, type: "impuesto"},
		method:'post',
    onSuccess: function(transport){
      var response = transport.responseText || "no response text";
			$('suggestionImpuestoDiv').show();
			$('suggestionImpuestoDiv').innerHTML = response;
			var elements = $$('span.resultSuggestImpuesto');
			AddSuggestImpuestoListener(elements);
		},
    onFailure: function(){ alert('Something went wrong...') }
  });
}

function HideSuggestions()
{
	$('suggestionDiv').hide();
}

function FillImpuestoData()
{
	$('loadingDivImpuesto').innerHTML = '<img src="'+WEB_ROOT+'/images/load.gif" />';

//	$('suggestionProductDiv').hide();
	new Ajax.Request(WEB_ROOT+'/ajax/fill_form.php',
	{
  	parameters: {value: $('impuestoId').value, type: "impuesto"},
		method:'post',
    onSuccess: function(transport){
      var response = transport.responseText || "no response text";
			var splitResponse = response.split("{#}");
			$('impuestoId').value = splitResponse[0];
			$('tasa').value = splitResponse[1];
			$('tipo').value = splitResponse[2];
			$('iva').value = splitResponse[3];
			$('loadingDivImpuesto').innerHTML = '';
		},
    onFailure: function(){ alert('Something went wrong...') }
  });

}

function FillConceptoData()
{
	$('loadingDivConcepto').innerHTML = '<img src="'+WEB_ROOT+'/images/load.gif" />';

//	$('suggestionProductDiv').hide();
	new Ajax.Request(WEB_ROOT+'/ajax/fill_form.php',
	{
  	parameters: {value: $('noIdentificacion').value, type: "producto"},
		method:'post',
    onSuccess: function(transport){
      var response = transport.responseText || "no response text";
			var splitResponse = response.split("{#}");
			$('descripcion').value = splitResponse[0];
			$('valorUnitario').value = splitResponse[1];
			$('unidad').value = splitResponse[2];
			$('loadingDivConcepto').innerHTML = '';
		},
    onFailure: function(){ alert('Something went wrong...') }
  });

}

function FillDatosFacturacion(id)
{
	$('loadingDivDatosFactura').innerHTML = '<img src="'+WEB_ROOT+'/images/load.gif" />';
//	$('suggestionDiv').hide();
	new Ajax.Request(WEB_ROOT+'/ajax/fill_form.php',
	{
  	parameters: {value: id, type: "datosFacturacion"},
		method:'post',
    onSuccess: function(transport){
      var response = transport.responseText || "no response text";
			var splitResponse = response.split("{#}");
			$('razonSocial').value = splitResponse[3];
			$('calle').value = splitResponse[4];
			$('noExt').value = splitResponse[5];
			$('noInt').value = splitResponse[6];
			$('colonia').value = splitResponse[7];
			$('municipio').value = splitResponse[8];
			$('estado').value = splitResponse[10];
			$('localidad').value = splitResponse[11];
			$('cp').value = splitResponse[9];
			$('pais').value = splitResponse[13];
			$('referencia').value = splitResponse[12];
			$('email').value = splitResponse[14];
			$('rfc').value = splitResponse[15];
			$('userId').value = splitResponse[16];
			$('loadingDivDatosFactura').innerHTML = '';

		},
    onFailure: function(){ alert('Something went wrong...') }
  });

}

function AgregarConcepto()
{
	$('conceptos').innerHTML = '<div align="center"><img src="'+WEB_ROOT+'/images/load.gif" /></div>';
	var form = $('conceptoForm').serialize();
	new Ajax.Request(WEB_ROOT+'/ajax/sistema.php',
	{
  	parameters: {form: form, type: "agregarConcepto"},
		method:'post',
    onSuccess: function(transport){
      var response = transport.responseText || "no response text";
			var splitResponse = response.split("|");

			if(splitResponse[0] == "fail")
			{
				$('divStatus').innerHTML = splitResponse[1];
				$('centeredDiv').show();
				grayOut(true);
			}
			$('conceptos').innerHTML = splitResponse[2];
			var elements = $$('span.linkBorrar');
			AddBorrarConceptoListeners(elements);

			UpdateTotalesDesglosados();
		},
    onFailure: function(){ alert('Something went wrong...') }
  });
}

function AgregarImpuesto()
{
	$('impuestos').innerHTML = '<div align="center"><img src="'+WEB_ROOT+'/images/load.gif" /></div>';
	var form = $('impuestoForm').serialize();
	new Ajax.Request(WEB_ROOT+'/ajax/sistema.php',
	{
  	parameters: {form: form, type: "agregarImpuesto"},
		method:'post',
    onSuccess: function(transport){
      var response = transport.responseText || "no response text";
		//	alert(response);
			var splitResponse = response.split("|");

			if(splitResponse[0] == "fail")
			{
				$('divStatus').innerHTML = splitResponse[1];
				$('centeredDiv').show();
				grayOut(true);
			}
			$('impuestos').innerHTML = splitResponse[2];
			var elements = $$('span.linkBorrarImpuesto');
			AddBorrarImpuestosListeners(elements);

			UpdateTotalesDesglosados();
		},
    onFailure: function(){ alert('Something went wrong...') }
  });
}


function BorrarConcepto(e, id)
{
	id = id.strip();
	new Ajax.Request(WEB_ROOT+'/ajax/sistema.php',
	{
  	parameters: {id: id, type: "borrarConcepto"},
		method:'post',
    onSuccess: function(transport){
      var response = transport.responseText || "no response text";
			$('conceptos').innerHTML = response;
			var elements = $$('span.linkBorrar');
			AddBorrarConceptoListeners(elements)
			UpdateTotalesDesglosados();

		},
    onFailure: function(){ alert('Something went wrong...') }
  });
}

function BorrarImpuesto(e, id)
{
	id = id.strip();
	new Ajax.Request(WEB_ROOT+'/ajax/sistema.php',
	{
  	parameters: {id: id, type: "borrarImpuesto"},
		method:'post',
    onSuccess: function(transport){
      var response = transport.responseText || "no response text";
			$('impuestos').innerHTML = response;
			var elements = $$('span.linkBorrarImpuesto');
			AddBorrarImpuestosListeners(elements)
			UpdateTotalesDesglosados();

		},
    onFailure: function(){ alert('Something went wrong...') }
  });
}

function AddBorrarConceptoListeners(elements)
{
	elements.each(
		function(e) {
			var id = $(e).up(0).previous(8).innerHTML;
			Event.observe(e, "click", function (e) {
				BorrarConcepto(e, id);
			});
		}
	);
}

function AddBorrarImpuestosListeners(elements)
{
	elements.each(
		function(e) {
			var id = $(e).up(0).previous(4).innerHTML;
			Event.observe(e, "click", function (e) {
				BorrarImpuesto(e, id);
			});
		}
	);
}

function UpdateTotalesDesglosados()
{
	var form = $('nuevaFactura').serialize();
	new Ajax.Request(WEB_ROOT+'/ajax/sistema.php',
	{
  	parameters: {form: form, type: "updateTotalesDesglosados"},
		method:'post',
    onSuccess: function(transport){
      var response = transport.responseText || "no response text";
			$('totalesDesglosadosDiv').innerHTML = response;
		},
    onFailure: function(){ alert('Something went wrong...') }
  });
}

function GenerarComprobante()
{
	var message = "Realmente deseas generar un comprobante. Asegurate de que lo estes generando para tu RFC Correcto.";
	if(!confirm(message))
	{
		return;
	}

	$('totalesDesglosadosDiv').innerHTML = '<img src="'+WEB_ROOT+'/images/load.gif" />Generando Comprobante, este proceso puede tardar unos segundos';

	$('nuevaFactura').enable();
	var nuevaFactura = $('nuevaFactura').serialize();
	$('nuevaFactura').disable();
	$('rfc').enable();
	$('userId').enable();
	$('formaDePago').enable();
	$('condicionesDePago').enable();
	$('metodoDePago').enable();
	$('tasaIva').enable();
	$('tiposDeMoneda').enable();
	$('porcentajeRetIva').enable();
	$('porcentajeDescuento').enable();
	$('tipoDeCambio').enable();
	$('porcentajeRetIsr').enable();
	$('tiposComprobanteId').enable();
	$('sucursalId').enable();
	$('porcentajeIEPS').enable();
	$('nuevaFactura').enable();

	if($('reviso')) var reviso = $('reviso').value;
	else var reviso = "";

	if($('autorizo')) var autorizo = $('autorizo').value;
	else var autorizo = "";

	if($('recibio')) var recibio = $('recibio').value;
	else var recibio = "";

	if($('vobo')) var vobo = $('vobo').value;
	else var vobo = "";

	if($('pago')) var pago = $('pago').value;
	else var pago = "";

	new Ajax.Request(WEB_ROOT+'/ajax/sistema.php',
	{
		parameters: {nuevaFactura: nuevaFactura, observaciones: $('observaciones').value, type: "generarComprobante", reviso: reviso, autorizo: autorizo, recibio: recibio, vobo: vobo, pago: pago},
		method:'post',
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("|");
			if(splitResponse[0] == "fail")
			{
				$('divStatus').innerHTML = splitResponse[1];
				$('centeredDiv').show();
				grayOut(true);
				$('totalesDesglosadosDiv').innerHTML = "";
			}
			else
			{
				// var resp = confirm("Desea enviar la factura por correo");
				// if(resp){
				// 	var comprobanteId = splitResp[2];
				// 	EnviarEmail(comprobanteId);
				// }
				$('totalesDesglosadosDiv').innerHTML = response;
				//$('reemplazarBoton').innerHTML += response;;
				//$('reemplazarBoton').innerHTML = splitResponse[1];;
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}
function EnviarEmail(id){

	new Ajax.Request(WEB_ROOT+'/ajax/manage-facturas.php',
	{
		method:'post',
		parameters: {type: 'enviar_email', id_comprobante: id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");
			if(splitResponse[0] == "ok"){
				ShowStatusPopUp(splitResponse[1])
			}
			else {
				ShowStatusPopUp(splitResponse[1])
			}


		},
		onFailure: function(){ alert('Something went wrong...') }
	});

}//EnviarEmail

function ShowPopUpDiv(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/popupdivtest.php',
	{
		method:'post',
    onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			$('fview').innerHTML = response;
			Event.observe($('closePopUpDiv'), "click", function(){ ShowPopUpDiv(0); });
			new Draggable('fview',{scroll:window,handle:'popupheader'});

		},
    onFailure: function(){ alert('Something went wrong...') }
  });
}

Event.observe(window, 'load', function() {
	if($('rfc'))
	{
		Event.observe($('rfc'), "keyup", function(){ SuggestUser();
			//FillDatosFacturacion();
			});
	}
	if($('rfc'))
	{
		var noIdentificacion = document.getElementById('noIdentificacion');
		if(typeof (noIdentificacion)!='undefined' && noIdentificacion !=null)
			Event.observe($('noIdentificacion'), "keyup", function(){ SuggestProduct(); FillConceptoData();});  // aki leox
	}
	if($('rfc'))
	{
        var conceptoDiv = document.getElementById('agregarConceptoDiv');
        if(typeof (conceptoDiv)!='undefined' && conceptoDiv !=null)
		Event.observe($('agregarConceptoDiv'), "click", AgregarConcepto);
	}
	if($('rfc'))
	{
        var generarFactura = document.getElementById('agregarConceptoDiv');
        if(typeof (generarFactura)!='undefined' && generarFactura !=null)
		Event.observe($('generarFactura'), "click", GenerarComprobante);
	}
	if($('rfc'))
	{
		if($('agregarImpuestoDiv'))
		{
			Event.observe($('agregarImpuestoDiv'), "click", AgregarImpuesto);
		}
        var vistaPrevia = document.getElementById('agregarConceptoDiv');
        if(typeof (vistaPrevia)!='undefined' && vistaPrevia !=null)
		Event.observe($('vistaPrevia'), "click", VistaPreviaComprobante);

	}

	if($('impuestoId'))
	{
		Event.observe($('impuestoId'), "keyup", function(){ SuggestImpuesto(); FillImpuestoData();});
	}

	if($$('span.linkBorrar'))
	{
		var elements = $$('span.linkBorrar');
		AddBorrarConceptoListeners(elements);
	}

		AddSuggestListener = function(e) {
			var el = e.element();
			var del = el.hasClassName('suggestUserDiv');
			var id = el.identify();
			if(del == true){
				FillRFC(1, id);
				return;
			}

			del = el.hasClassName('suggestProductoDiv');

			if(del == true){
				FillNoIdentificacion(1, id);
				return;
			}

			del = el.hasClassName('suggestImpuestoDiv');

			if(del == true){
				FillImpuestoId(1, id);
				return;
			}

			del = el.hasClassName('closeSuggestUserDiv');

			if(del == true){
				$('suggestionDiv').hide();
				return;
			}

			del = el.hasClassName('closeSuggestProductoDiv');

			if(del == true){
				$('suggestionProductDiv').hide();
				return;
			}

			del = el.hasClassName('closeSuggestImpuestoDiv');

			if(del == true){
				$('suggestionImpuestoDiv').hide();
				return;
			}


		}

	if($('divForm')!= undefined)
	{
		$('divForm').observe("click", AddSuggestListener);
	}



});




