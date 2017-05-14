Event.observe(window, 'load', function() {
	Event.observe($('addTipoRequerimiento'), "click", AddTipoRequerimientoDiv);

	AddEditTipoRequerimientoListeners = function(e) {
		var el = e.element();
		var del = el.hasClassName('spanDelete');
		var id = el.identify();
		if(del == true)
		{
			DeleteTipoRequerimientoPopup(id);
			return;
		}

		del = el.hasClassName('spanEdit');
		if(del == true)
		{
			EditTipoRequerimientoPopup(id);
		}
	}

	$('contenido').observe("click", AddEditTipoRequerimientoListeners);

});

function EditTipoRequerimientoPopup(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/tipoRequerimiento.php',
	{
		method:'post',
		parameters: {type: "editTipoRequerimiento", tipoRequerimientoId:id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('closePopUpDiv'), "click", function(){ EditTipoRequerimientoPopup(0); });
			Event.observe($('editTipoRequerimiento'), "click", EditTipoRequerimiento);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function EditTipoRequerimiento()
{
	new Ajax.Request(WEB_ROOT+'/ajax/tipoRequerimiento.php',
	{
		method:'post',
		parameters: $('editTipoRequerimientoForm').serialize(true),
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
				AddTipoRequerimientoDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function DeleteTipoRequerimientoPopup(id)
{
	var message = "Realmente deseas eliminar este registro?";
	if(!confirm(message))
	{
		return;
	}
	new Ajax.Request(WEB_ROOT+'/ajax/tipoRequerimiento.php',
	{
		method:'post',
		parameters: {type: "deleteTipoRequerimiento", tipoRequerimientoId: id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");
			ShowStatus(splitResponse[1])
			$('contenido').innerHTML = splitResponse[2];
				AddTipoRequerimientoDiv(0);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddTipoRequerimientoDiv(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/tipoRequerimiento.php',
	{
		method:'post',
		parameters: {type: "addTipoRequerimiento"},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('addTipoRequerimientoButton'), "click", AddTipoRequerimiento);
			Event.observe($('fviewclose'), "click", function(){ AddTipoRequerimientoDiv(0); });
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddTipoRequerimiento()
{
	new Ajax.Request(WEB_ROOT+'/ajax/tipoRequerimiento.php',
	{
		method:'post',
		parameters: $('addTipoRequerimientoForm').serialize(true),
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
				AddTipoRequerimientoDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

