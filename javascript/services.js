Event.observe(window, 'load', function() {
	//Event.observe($('addCustomer'), "click", AddCustomerDiv);

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
			EditServicioPopup(id);
		}
	}

	$('contenido').observe("click", AddEditCustomerListeners);

});

function Historial(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/services.php',
	{
		method:'post',
		parameters: {type: "historial", servicioId:id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('closePopUpDiv'), "click", function(){ EditServicioPopup(0); });
			Event.observe($('editCustomer'), "click", EditServicio);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function EditServicioPopup(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/services.php',
	{
		method:'post',
		parameters: {type: "editServicio", servicioId:id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('closePopUpDiv'), "click", function(){ EditServicioPopup(0); });
			Event.observe($('editCustomer'), "click", EditServicio);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function EditServicio()
{
	new Ajax.Request(WEB_ROOT+'/ajax/services.php',
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
	var message = "Realmente deseas eliminar este servicio?";
	if(!confirm(message))
	{
		return;
	}
	new Ajax.Request(WEB_ROOT+'/ajax/services.php',
	{
		method:'post',
		parameters: {type: "deleteService", servicioId: id, contractId: $('contractId').value},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");
			ShowStatus(splitResponse[1]);
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

function UpdateCosto()
{
	new Ajax.Request(WEB_ROOT+'/ajax/services.php',
	{
		method:'post',
		parameters: {type:"updateCosto", id:$('tipoServicioId').value},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			$('costo').value = response;
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}
