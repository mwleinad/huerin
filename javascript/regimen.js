Event.observe(window, 'load', function() {
	Event.observe($('addRegimen'), "click", AddRegimenDiv);

	AddEditRegimenListeners = function(e) {
		var el = e.element();
		var del = el.hasClassName('spanDelete');
		var id = el.identify();
		if(del == true)
		{
			DeleteRegimenPopup(id);
			return;
		}

		del = el.hasClassName('spanEdit');
		if(del == true)
		{
			EditRegimenPopup(id);
		}
	}

	$('content').observe("click", AddEditRegimenListeners);

});

function EditRegimenPopup(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/regimen.php',
	{
		method:'post',
		parameters: {type: "editRegimen", regimenId:id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('closePopUpDiv'), "click", function(){ EditRegimenPopup(0); });
			Event.observe($('editRegimen'), "click", EditRegimen);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function EditRegimen()
{
	new Ajax.Request(WEB_ROOT+'/ajax/regimen.php',
	{
		method:'post',
		parameters: $('editRegimenForm').serialize(true),
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
				AddRegimenDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function DeleteRegimenPopup(id)
{
	var message = "Realmente deseas eliminar este registro?";
	if(!confirm(message))
	{
		return;
	}
	new Ajax.Request(WEB_ROOT+'/ajax/regimen.php',
	{
		method:'post',
		parameters: {type: "deleteRegimen", regimenId: id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");
			ShowStatus(splitResponse[1])
			$('contenido').innerHTML = splitResponse[2];
				AddRegimenDiv(0);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddRegimenDiv(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/regimen.php',
	{
		method:'post',
		parameters: {type: "addRegimen"},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('addRegimenButton'), "click", AddRegimen);
			Event.observe($('fviewclose'), "click", function(){ AddRegimenDiv(0); });
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddRegimen()
{
	new Ajax.Request(WEB_ROOT+'/ajax/regimen.php',
	{
		method:'post',
		parameters: $('addRegimenForm').serialize(true),
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
				AddRegimenDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

