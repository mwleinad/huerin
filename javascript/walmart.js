Event.observe(window, 'load', function() {
	Event.observe($('addWallmart'), "click", AddWallmartDiv);

	AddEditWallmartListeners = function(e) {
		var el = e.element();
		var del = el.hasClassName('spanDelete');
		var id = el.identify();
		if(del == true)
		{
			DeleteWallmartPopup(id);
			return;
		}

		del = el.hasClassName('spanEdit');
		if(del == true)
		{
			EditWallmartPopup(id);
		}
	}

	$('contenido').observe("click", AddEditWallmartListeners);

});

function EditWallmartPopup(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/wallmart.php',
	{
		method:'post',
		parameters: {type: "editWallmart", wallmartId:id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('closePopUpDiv'), "click", function(){ EditWallmartPopup(0); });
			Event.observe($('editWallmart'), "click", EditWallmart);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function EditWallmart()
{
	new Ajax.Request(WEB_ROOT+'/ajax/wallmart.php',
	{
		method:'post',
		parameters: $('editWallmartForm').serialize(true),
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
				AddWallmartDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function DeleteWallmartPopup(id)
{
	var message = "Realmente deseas eliminar este usuario?";
	if(!confirm(message))
	{
		return;
	}
	new Ajax.Request(WEB_ROOT+'/ajax/wallmart.php',
	{
		method:'post',
		parameters: {type: "deleteWallmart", wallmartId: id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";

			var splitResponse = response.split("[#]");
			ShowStatus(splitResponse[1])
			$('contenido').innerHTML = splitResponse[2];
				AddWallmartDiv(0);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddWallmartDiv(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/wallmart.php',
	{
		method:'post',
		parameters: {type: "addWallmart"},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('btnAddWallmart'), "click", AddWallmart);
			Event.observe($('fviewclose'), "click", function(){ AddWallmartDiv(0); });
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddWallmart()
{
	new Ajax.Request(WEB_ROOT+'/ajax/wallmart.php',
	{
		method:'post',
		parameters: $('addWallmartForm').serialize(true),
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
				AddWallmartDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

