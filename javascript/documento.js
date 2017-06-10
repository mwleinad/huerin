Event.observe(window, 'load', function() {
	Event.observe($('addDocumento'), "click", AddDocumentoDiv);

	AddEditDocumentoListeners = function(e) {
		var el = e.element();
		var del = el.hasClassName('spanDelete');
		var id = el.identify();
		if(del == true)
		{
			DeleteDocumentoPopup(id);
			return;
		}

		del = el.hasClassName('spanEdit');
		if(del == true)
		{
			EditDocumentoPopup(id);
		}
	}

	$('content').observe("click", AddEditDocumentoListeners);

});

function EditDocumentoPopup(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/documento.php',
	{
		method:'post',
		parameters: {type: "editDocumento", documentoId:id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('closePopUpDiv'), "click", function(){ EditDocumentoPopup(0); });
			Event.observe($('editDocumento'), "click", EditDocumento);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function EditDocumento()
{
	new Ajax.Request(WEB_ROOT+'/ajax/documento.php',
	{
		method:'post',
		parameters: $('editDocumentoForm').serialize(true),
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
				$('content').innerHTML = splitResponse[2];
				AddDocumentoDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function DeleteDocumentoPopup(id)
{
	var message = "Realmente deseas eliminar este registro?";
	if(!confirm(message))
	{
		return;
	}
	new Ajax.Request(WEB_ROOT+'/ajax/documento.php',
	{
		method:'post',
		parameters: {type: "deleteDocumento", documentoId: id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");
			ShowStatus(splitResponse[1])
			$('content').innerHTML = splitResponse[2];
				AddDocumentoDiv(0);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddDocumentoDiv(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/documento.php',
	{
		method:'post',
		parameters: {type: "addDocumento"},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('addDocumento'), "click", AddDocumento);
			Event.observe($('fviewclose'), "click", function(){ AddDocumentoDiv(0); });
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddDocumento()
{
	new Ajax.Request(WEB_ROOT+'/ajax/documento.php',
	{
		method:'post',
		parameters: $('addDocumentoForm').serialize(true),
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
				$('content').innerHTML = splitResponse[2];
				AddDocumentoDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

