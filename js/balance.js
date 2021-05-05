Event.observe(window, 'load', function() {
	if($('addCustomer'))
	{
		Event.observe($('addCustomer'), "click", AddCustomerDiv);
	}

	AddEditCustomerListeners = function(e) {
		var el = e.element();
		var del = el.hasClassName('spanDelete');
		var id = el.identify();
		if(del == true)
		{
			DeleteCustomerPopup(id);
			return;
		}

		del = el.hasClassName('spanEdit');
		if(del == true)
		{
			EditCustomerPopup(id);
		}

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
//	$('contenido').observe("click", AddEditCustomerListeners);
	$('rfc').observe("keyup", SuggestUser);

	if($('divForm')!= undefined)
	{
		$('divForm').observe("click", AddSuggestListener);
	}
});

function EliminarInactivos()
{
	var message = "Realmente deseas eliminar los clientes inactivos? No podras revertir este proceso";
	if(!confirm(message))
	{
		return;
	}

	window.location = WEB_ROOT+"/customer/delete/inactivos";
}
function Search()
{
	if($('rfc').value.length < 2)
	{
		return;
	}
	new Ajax.Request(WEB_ROOT+'/ajax/customer.php',
	{
		method:'post',
		parameters: {valur: $('rfc').value, tipo: $('type').value, type: "search"},
    onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			$('contenido').innerHTML = response;
		},
    onFailure: function(){ alert('Something went wrong...') }
  });
}

function SuggestUser()
{
	new Ajax.Request(WEB_ROOT+'/ajax/suggest_customer_contract.php',
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
	FillDatos(id);
}

function FillDatos(id)
{
	$('loadingDivDatosFactura').innerHTML = '<img src="'+WEB_ROOT+'/images/load.gif" />';

//	$('suggestionDiv').hide();
	new Ajax.Request(WEB_ROOT+'/ajax/fill_form_servicios_cliente_nocontract.php',
	{
  	parameters: {value: id, type: "datosRazon"},
		method:'post',
    onSuccess: function(transport){
      var response = transport.responseText || "no response text";
			var splitResponse = response.split("{#}");
			$('rfc').value = splitResponse[0];
			$('cuenta').value = id;
			$('loadingDivDatosFactura').innerHTML = '';
			//BuscarServiciosActivos();
		},
    onFailure: function(){ alert('Something went wrong...') }
  });
}

function BuscarServiciosActivos()
{
	new Ajax.Request(WEB_ROOT + '/ajax/balance.php',
   {
	  method : 'post',
	  parameters : 'type=search&id=' + $('cuenta').value+'&month=' + $('month').value+'&year=' + $('year').value,
	  onSuccess : function(transporta)
	  {
		  var respuesta = transporta.responseText;
			$('contenido').innerHTML = respuesta
	  },
	  onFailure : function()
				  {
					  alert('Se detecto un problema con el servidor');
				  }
   });
}

function EditCustomerPopup(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/customer.php',
	{
		method:'post',
		parameters: {type: "editCustomer", customerId:id, valur:$('rfc').value, tipo: $('type').value},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('closePopUpDiv'), "click", function(){ EditCustomerPopup(0); });
			Event.observe($('editCustomer'), "click", EditCustomer);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function EditCustomer()
{
	new Ajax.Request(WEB_ROOT+'/ajax/customer.php',
	{
		method:'post',
		parameters: $('editCustomerForm').serialize(true),
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");
			if(splitResponse[0] == "fail")
			{
				ShowStatusPopUp(splitResponse[1])
			}
			else
			{
				ShowStatusPopUp(splitResponse[1])
				$('contenido').innerHTML = splitResponse[2];
				AddCustomerDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function DeleteCustomerPopup(id)
{
	var message = "Realmente deseas cambiar el estatus de este cliente?";
	if(!confirm(message))
	{
		return;
	}
	new Ajax.Request(WEB_ROOT+'/ajax/customer.php',
	{
		method:'post',
		parameters: {type: "deleteCustomer", customerId: id, valur:$('rfc').value, tipo: $('type').value},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");
			ShowStatus(splitResponse[1])
			$('contenido').innerHTML = splitResponse[2];
				AddCustomerDiv(0);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddCustomerDiv(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/customer.php',
	{
		method:'post',
		parameters: {type: "addCustomer"},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('btnAddCustomer'), "click", AddCustomer);
			Event.observe($('fviewclose'), "click", function(){ AddCustomerDiv(0); });
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddCustomer()
{
	new Ajax.Request(WEB_ROOT+'/ajax/customer.php',
	{
		method:'post',
		parameters: $('addCustomerForm').serialize(true),
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");
			if(splitResponse[0] == "fail")
			{
				ShowStatusPopUp(splitResponse[1])
			}
			else
			{
				ShowStatusPopUp(splitResponse[1])
				$('contenido').innerHTML = splitResponse[2];
				AddCustomerDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

