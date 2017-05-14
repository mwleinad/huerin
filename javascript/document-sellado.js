Event.observe(window, 'load', function() {
	Event.observe($('addDocument'), "click", AddDocumentDiv);

	AddEditDocumentListeners = function(e) {
		var el = e.element();
		var del = el.hasClassName('spanDelete');
		var id = el.identify();
		if(del == true)
		{
			DeleteDocumentPopup(id);
			return;
		}

		del = el.hasClassName('spanEdit');
		if(del == true)
		{
			EditDocumentPopup(id);
		}
	}

	$('contenido').observe("click", AddEditDocumentListeners);

});

function EditDocumentPopup(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/document-sellado.php',
	{
		method:'post',
		parameters: {type: "editDocument", docSelladoId:id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			
			FViewOffSet(response);
			Event.observe($('closePopUpDiv'), "click", function(){ EditDocumentPopup(0); });
			Event.observe($('editDocument'), "click", EditDocument);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function EditDocument()
{
	new Ajax.Request(WEB_ROOT+'/ajax/document-sellado.php',
	{
		method:'post',
		parameters: $('editDocumentForm').serialize(true),
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
				AddDocumentDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function DeleteDocumentPopup(id)
{
	var message = "Realmente deseas eliminar este documento?";
	if(!confirm(message))
	{
		return;
	}
	new Ajax.Request(WEB_ROOT+'/ajax/document-sellado.php',
	{
		method:'post',
		parameters: {type: "deleteDocument", docSelladoId: id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");
			ShowStatus(splitResponse[1])
			$('contenido').innerHTML = splitResponse[2];
				AddDocumentDiv(0);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddDocumentDiv(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/document-sellado.php',
	{
		method:'post',
		parameters: {type: "addDocument"},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('btnAddDocument'), "click", AddDocument);
			Event.observe($('fviewclose'), "click", function(){ AddDocumentDiv(0); });
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddDocument()
{
	new Ajax.Request(WEB_ROOT+'/ajax/document-sellado.php',
	{
		method:'post',
		parameters: $('addDocumentForm').serialize(true),
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
				AddDocumentDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

