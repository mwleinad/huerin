Event.observe(window, 'load', function() {
	Event.observe($('addObligacion'), "click", AddObligacionDiv);

	AddEditObligacionListeners = function(e) {
		var el = e.element();
		var del = el.hasClassName('spanDelete');
		var id = el.identify();
		if(del == true)
		{
			DeleteObligacionPopup(id);
			return;
		}

		del = el.hasClassName('spanEdit');
		if(del == true)
		{
			EditObligacionPopup(id);
		}
	}

	$('content').observe("click", AddEditObligacionListeners);

});

function EditObligacionPopup(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/obligacion.php',
	{
		method:'post',
		parameters: {type: "editObligacion", obligacionId:id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('closePopUpDiv'), "click", function(){ EditObligacionPopup(0); });
			Event.observe($('editObligacion'), "click", EditObligacion);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function EditObligacion()
{
	new Ajax.Request(WEB_ROOT+'/ajax/obligacion.php',
	{
		method:'post',
		parameters: $('editObligacionForm').serialize(true),
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
				AddObligacionDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function DeleteObligacionPopup(id)
{
	var message = "Realmente deseas eliminar este registro?";
	if(!confirm(message))
	{
		return;
	}
	new Ajax.Request(WEB_ROOT+'/ajax/obligacion.php',
	{
		method:'post',
		parameters: {type: "deleteObligacion", obligacionId: id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");
			ShowStatus(splitResponse[1])
			$('contenido').innerHTML = splitResponse[2];
				AddObligacionDiv(0);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddObligacionDiv(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/obligacion.php',
	{
		method:'post',
		parameters: {type: "addObligacion"},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('addObligacionButton'), "click", AddObligacion);
			Event.observe($('fviewclose'), "click", function(){ AddObligacionDiv(0); });
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddObligacion()
{
	new Ajax.Request(WEB_ROOT+'/ajax/obligacion.php',
	{
		method:'post',
		parameters: $('addObligacionForm').serialize(true),
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
				AddObligacionDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

