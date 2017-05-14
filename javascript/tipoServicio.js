Event.observe(window, 'load', function() {
	Event.observe($('addTipoServicio'), "click", AddTipoServicioDiv);

	AddEditTipoServicioListeners = function(e) {
		var el = e.element();
		var del = el.hasClassName('spanDelete');
		var id = el.identify();
		if(del == true)
		{
			DeleteTipoServicioPopup(id);
			return;
		}

		del = el.hasClassName('spanEdit');
		if(del == true)
		{
			EditTipoServicioPopup(id);
		}
	}

	$('contenido').observe("click", AddEditTipoServicioListeners);

});

function EditTipoServicioPopup(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/tipoServicio.php',
	{
		method:'post',
		parameters: {type: "editTipoServicio", tipoServicioId:id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('closePopUpDiv'), "click", function(){ EditTipoServicioPopup(0); });
			Event.observe($('editTipoServicio'), "click", EditTipoServicio);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function EditTipoServicio()
{
	new Ajax.Request(WEB_ROOT+'/ajax/tipoServicio.php',
	{
		method:'post',
		parameters: $('editTipoServicioForm').serialize(true),
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
				AddTipoServicioDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function DeleteTipoServicioPopup(id)
{
	var message = "Realmente deseas eliminar este registro?";
	if(!confirm(message))
	{
		return;
	}
	new Ajax.Request(WEB_ROOT+'/ajax/tipoServicio.php',
	{
		method:'post',
		parameters: {type: "deleteTipoServicio", tipoServicioId: id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");
			ShowStatus(splitResponse[1])
			$('contenido').innerHTML = splitResponse[2];
				AddTipoServicioDiv(0);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddTipoServicioDiv(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/tipoServicio.php',
	{
		method:'post',
		parameters: {type: "addTipoServicio"},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('addTipoServicioButton'), "click", AddTipoServicio);
			Event.observe($('fviewclose'), "click", function(){ AddTipoServicioDiv(0); });
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddTipoServicio()
{
	new Ajax.Request(WEB_ROOT+'/ajax/tipoServicio.php',
	{
		method:'post',
		parameters: $('addTipoServicioForm').serialize(true),
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
				AddTipoServicioDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

