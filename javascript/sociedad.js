Event.observe(window, 'load', function() {
	Event.observe($('addSociedad'), "click", AddSociedadDiv);

	AddEditSociedadListeners = function(e) {
		var el = e.element();
		var del = el.hasClassName('spanDelete');
		var id = el.identify();
		if(del == true)
		{
			DeleteSociedadPopup(id);
			return;
		}

		del = el.hasClassName('spanEdit');
		if(del == true)
		{
			EditSociedadPopup(id);
		}
	}

	$('content').observe("click", AddEditSociedadListeners);

});

function EditSociedadPopup(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/sociedad.php',
	{
		method:'post',
		parameters: {type: "editSociedad", sociedadId:id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('closePopUpDiv'), "click", function(){ EditSociedadPopup(0); });
			Event.observe($('editSociedad'), "click", EditSociedad);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function EditSociedad()
{
	new Ajax.Request(WEB_ROOT+'/ajax/sociedad.php',
	{
		method:'post',
		parameters: $('editSociedadForm').serialize(true),
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
				AddSociedadDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function DeleteSociedadPopup(id)
{
	var message = "Realmente deseas eliminar este registro?";
	if(!confirm(message))
	{
		return;
	}
	new Ajax.Request(WEB_ROOT+'/ajax/sociedad.php',
	{
		method:'post',
		parameters: {type: "deleteSociedad", sociedadId: id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");
			ShowStatus(splitResponse[1])
			$('contenido').innerHTML = splitResponse[2];
				AddSociedadDiv(0);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddSociedadDiv(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/sociedad.php',
	{
		method:'post',
		parameters: {type: "addSociedad"},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('addSociedadButton'), "click", AddSociedad);
			Event.observe($('fviewclose'), "click", function(){ AddSociedadDiv(0); });
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddSociedad()
{
	new Ajax.Request(WEB_ROOT+'/ajax/sociedad.php',
	{
		method:'post',
		parameters: $('addSociedadForm').serialize(true),
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
				AddSociedadDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

