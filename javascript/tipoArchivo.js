Event.observe(window, 'load', function() {
	Event.observe($('addTipoArchivo'), "click", AddTipoArchivoDiv);

	AddEditTipoArchivoListeners = function(e) {
		var el = e.element();
		var del = el.hasClassName('spanDelete');
		var id = el.identify();
		if(del == true)
		{
			DeleteTipoArchivoPopup(id);
			return;
		}

		del = el.hasClassName('spanEdit');
		if(del == true)
		{
			EditTipoArchivoPopup(id);
		}
	}

	$('contenido').observe("click", AddEditTipoArchivoListeners);

});

function EditTipoArchivoPopup(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/tipoArchivo.php',
	{
		method:'post',
		parameters: {type: "editTipoArchivo", tipoArchivoId:id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('closePopUpDiv'), "click", function(){ EditTipoArchivoPopup(0); });
			Event.observe($('editTipoArchivo'), "click", EditTipoArchivo);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function EditTipoArchivo()
{
	new Ajax.Request(WEB_ROOT+'/ajax/tipoArchivo.php',
	{
		method:'post',
		parameters: $('editTipoArchivoForm').serialize(true),
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
				AddTipoArchivoDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function DeleteTipoArchivoPopup(id)
{
	var message = "Realmente deseas eliminar este registro?";
	if(!confirm(message))
	{
		return;
	}
	new Ajax.Request(WEB_ROOT+'/ajax/tipoArchivo.php',
	{
		method:'post',
		parameters: {type: "deleteTipoArchivo", tipoArchivoId: id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");
			ShowStatus(splitResponse[1])
			$('contenido').innerHTML = splitResponse[2];
				AddTipoArchivoDiv(0);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddTipoArchivoDiv(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/tipoArchivo.php',
	{
		method:'post',
		parameters: {type: "addTipoArchivo"},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('addTipoArchivoButton'), "click", AddTipoArchivo);
			Event.observe($('fviewclose'), "click", function(){ AddTipoArchivoDiv(0); });
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddTipoArchivo()
{
	new Ajax.Request(WEB_ROOT+'/ajax/tipoArchivo.php',
	{
		method:'post',
		parameters: $('addTipoArchivoForm').serialize(true),
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
				AddTipoArchivoDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

