Event.observe(window, 'load', function() {

	AddEditArchivoListeners = function(e) {
		var el = e.element();
		var del = el.hasClassName('spanDelete');
		var id = el.identify();
		if(del == true)
		{
			DeleteArchivoPopup(id);
			return;
		}

		del = el.hasClassName('spanEdit');
		if(del == true)
		{
			EditArchivoPopup(id);
		}
	}

	$('contentArchivos').observe("click", AddEditArchivoListeners);

});

function EditArchivoPopup(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/archivos.php',
	{
		method:'post',
		parameters: {type: "editArchivo", archivoId:id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('closePopUpDiv'), "click", function(){ EditArchivoPopup(0); });
			Event.observe($('editArchivo'), "click", EditArchivo);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function DeleteArchivoPopup(id,depa)
{
	var message = "Realmente deseas eliminar este registro?????";
	if(!confirm(message))
	{
		return;
	}
	new Ajax.Request(WEB_ROOT+'/ajax/archivos.php',
	{
		method:'post',
		parameters: {type: "deleteArchivo", archivoId: id, depa: depa},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");
			//ShowStatus(splitResponse[1])
			$('contenido').innerHTML = splitResponse[2];
				AddArchivoDiv(0);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function NuevoArchivo(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/archivos.php',
	{
		method:'post',
		parameters: {type: "addArchivo", id: id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			//Event.observe($('addArchivo'), "click", AddArchivo);
			Event.observe($('fviewclose'), "click", function(){ NuevoArchivo(0); });
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddArchivo()
{
	new Ajax.Request(WEB_ROOT+'/ajax/archivo.php',
	{
		method:'post',
		parameters: $('addArchivoForm').serialize(true),
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
				$('contentArchivos').innerHTML = splitResponse[2];
				AddArchivoDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

