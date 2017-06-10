Event.observe(window, 'load', function() {
	Event.observe($('addTipoDocumento'), "click", AddTipoDocumentoDiv);

	AddEditTipoDocumentoListeners = function(e) {
		var el = e.element();
		var del = el.hasClassName('spanDelete');
		var id = el.identify();
		if(del == true)
		{
			DeleteTipoDocumentoPopup(id);
			return;
		}

		del = el.hasClassName('spanEdit');
		if(del == true)
		{
			EditTipoDocumentoPopup(id);
		}
	}

	$('contenido').observe("click", AddEditTipoDocumentoListeners);

});

function EditTipoDocumentoPopup(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/tipoDocumento.php',
	{
		method:'post',
		parameters: {type: "editTipoDocumento", tipoDocumentoId:id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('closePopUpDiv'), "click", function(){ EditTipoDocumentoPopup(0); });
			Event.observe($('editTipoDocumento'), "click", EditTipoDocumento);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function EditTipoDocumento()
{
	new Ajax.Request(WEB_ROOT+'/ajax/tipoDocumento.php',
	{
		method:'post',
		parameters: $('editTipoDocumentoForm').serialize(true),
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
				AddTipoDocumentoDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function DeleteTipoDocumentoPopup(id)
{
	var message = "Realmente deseas eliminar este registro?";
	if(!confirm(message))
	{
		return;
	}
	new Ajax.Request(WEB_ROOT+'/ajax/tipoDocumento.php',
	{
		method:'post',
		parameters: {type: "deleteTipoDocumento", tipoDocumentoId: id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");
			ShowStatus(splitResponse[1])
			$('contenido').innerHTML = splitResponse[2];
				AddTipoDocumentoDiv(0);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddTipoDocumentoDiv(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/tipoDocumento.php',
	{
		method:'post',
		parameters: {type: "addTipoDocumento"},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('addTipoDocumentoButton'), "click", AddTipoDocumento);
			Event.observe($('fviewclose'), "click", function(){ AddTipoDocumentoDiv(0); });
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddTipoDocumento()
{
	new Ajax.Request(WEB_ROOT+'/ajax/tipoDocumento.php',
	{
		method:'post',
		parameters: $('addTipoDocumentoForm').serialize(true),
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
				AddTipoDocumentoDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

