Event.observe(window, 'load', function() {
	Event.observe($('addCity'), "click", AddCityDiv);

	AddEditCityListeners = function(e) {
		var el = e.element();
		var del = el.hasClassName('spanDelete');
		var id = el.identify();
		if(del == true)
		{
			DeleteCityPopup(id);
			return;
		}

		del = el.hasClassName('spanEdit');
		if(del == true)
		{
			EditCityPopup(id);
		}
	}

	$('contenido').observe("click", AddEditCityListeners);

});

function EditCityPopup(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/city.php',
	{
		method:'post',
		parameters: {type: "editCity", cityId:id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('closePopUpDiv'), "click", function(){ EditCityPopup(0); });
			Event.observe($('editCity'), "click", EditCity);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function EditCity()
{
	new Ajax.Request(WEB_ROOT+'/ajax/city.php',
	{
		method:'post',
		parameters: $('editCityForm').serialize(true),
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
				AddCityDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function DeleteCityPopup(id)
{
	var message = "Realmente deseas eliminar este estado?";
	if(!confirm(message))
	{
		return;
	}
	new Ajax.Request(WEB_ROOT+'/ajax/city.php',
	{
		method:'post',
		parameters: {type: "deleteCity", cityId: id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";

			var splitResponse = response.split("[#]");
			ShowStatus(splitResponse[1])
			$('contenido').innerHTML = splitResponse[2];
				AddCityDiv(0);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddCityDiv(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/city.php',
	{
		method:'post',
		parameters: {type: "addCity"},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('btnAddCity'), "click", AddCity);
			Event.observe($('fviewclose'), "click", function(){ AddCityDiv(0); });
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddCity()
{
	new Ajax.Request(WEB_ROOT+'/ajax/city.php',
	{
		method:'post',
		parameters: $('addCityForm').serialize(true),
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
				AddCityDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

