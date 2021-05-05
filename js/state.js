Event.observe(window, 'load', function() {
	Event.observe($('addState'), "click", AddStateDiv);

	AddEditStateListeners = function(e) {
		var el = e.element();
		var del = el.hasClassName('spanDelete');
		var id = el.identify();
		if(del == true)
		{
			DeleteStatePopup(id);
			return;
		}

		del = el.hasClassName('spanEdit');
		if(del == true)
		{
			EditStatePopup(id);
		}
	}

	$('contenido').observe("click", AddEditStateListeners);

});

function EditStatePopup(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/state.php',
	{
		method:'post',
		parameters: {type: "editState", stateId:id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('closePopUpDiv'), "click", function(){ EditStatePopup(0); });
			Event.observe($('editState'), "click", EditState);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function EditState()
{
	new Ajax.Request(WEB_ROOT+'/ajax/state.php',
	{
		method:'post',
		parameters: $('editStateForm').serialize(true),
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
				AddStateDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function DeleteStatePopup(id)
{
	var message = "Realmente deseas eliminar este estado?";
	if(!confirm(message))
	{
		return;
	}
	new Ajax.Request(WEB_ROOT+'/ajax/state.php',
	{
		method:'post',
		parameters: {type: "deleteState", stateId: id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";

			var splitResponse = response.split("[#]");
			ShowStatus(splitResponse[1])
			$('contenido').innerHTML = splitResponse[2];
				AddStateDiv(0);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddStateDiv(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/state.php',
	{
		method:'post',
		parameters: {type: "addState"},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('btnAddState'), "click", AddState);
			Event.observe($('fviewclose'), "click", function(){ AddStateDiv(0); });
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddState()
{
	new Ajax.Request(WEB_ROOT+'/ajax/state.php',
	{
		method:'post',
		parameters: $('addStateForm').serialize(true),
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
				AddStateDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

