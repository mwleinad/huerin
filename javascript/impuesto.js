Event.observe(window, 'load', function() {
	Event.observe($('addImpuesto'), "click", AddImpuestoDiv);

	AddEditImpuestoListeners = function(e) {
		var el = e.element();
		var del = el.hasClassName('spanDelete');
		var id = el.identify();
		if(del == true)
		{
			DeleteImpuestoPopup(id);
			return;
		}

		del = el.hasClassName('spanEdit');
		if(del == true)
		{
			EditImpuestoPopup(id);
		}
	}

	$('content').observe("click", AddEditImpuestoListeners);

});

function EditImpuestoPopup(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/impuesto.php',
	{
		method:'post',
		parameters: {type: "editImpuesto", impuestoId:id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('closePopUpDiv'), "click", function(){ EditImpuestoPopup(0); });
			Event.observe($('editImpuesto'), "click", EditImpuesto);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function EditImpuesto()
{
	new Ajax.Request(WEB_ROOT+'/ajax/impuesto.php',
	{
		method:'post',
		parameters: $('editImpuestoForm').serialize(true),
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
				AddImpuestoDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function DeleteImpuestoPopup(id)
{
	var message = "Realmente deseas eliminar este registro?";
	if(!confirm(message))
	{
		return;
	}
	new Ajax.Request(WEB_ROOT+'/ajax/impuesto.php',
	{
		method:'post',
		parameters: {type: "deleteImpuesto", impuestoId: id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");
			ShowStatus(splitResponse[1])
			$('contenido').innerHTML = splitResponse[2];
				AddImpuestoDiv(0);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddImpuestoDiv(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/impuesto.php',
	{
		method:'post',
		parameters: {type: "addImpuesto"},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('addImpuestoButton'), "click", AddImpuesto);
			Event.observe($('fviewclose'), "click", function(){ AddImpuestoDiv(0); });
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddImpuesto()
{
	new Ajax.Request(WEB_ROOT+'/ajax/impuesto.php',
	{
		method:'post',
		parameters: $('addImpuestoForm').serialize(true),
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
				AddImpuestoDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

