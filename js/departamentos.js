Event.observe(window, 'load', function() {
	Event.observe($('addDepartamentos'), "click", AddDepartamentosDiv);

	AddEditDepartamentosListeners = function(e) {
		var el = e.element();
		var del = el.hasClassName('spanDelete');
		var id = el.identify();
		if(del == true)
		{
			DeleteDepartamentosPopup(id);
			return;
		}

		del = el.hasClassName('spanEdit');
		if(del == true)
		{
			EditDepartamentosPopup(id);
		}
	}

	$('contenido').observe("click", AddEditDepartamentosListeners);

});

function EditDepartamentosPopup(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/departamentos.php',
	{
		method:'post',
		parameters: {type: "editDepartamentos", departamentoId:id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('closePopUpDiv'), "click", function(){ EditDepartamentosPopup(0); });
			Event.observe($('editDepartamentos'), "click", EditDepartamentos);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function EditDepartamentos()
{
	new Ajax.Request(WEB_ROOT+'/ajax/departamentos.php',
	{
		method:'post',
		parameters: $('editDepartamentosForm').serialize(true),
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
				AddDepartamentosDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function DeleteDepartamentosPopup(id)
{
	var message = "Realmente deseas eliminar este registro?";
	if(!confirm(message))
	{
		return;
	}
	new Ajax.Request(WEB_ROOT+'/ajax/departamentos.php',
	{
		method:'post',
		parameters: {type: "deleteDepartamentos", departamentoId: id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");
			ShowStatus(splitResponse[1])
			$('contenido').innerHTML = splitResponse[2];
				AddDepartamentosDiv(0);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddDepartamentosDiv(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/departamentos.php',
	{
		method:'post',
		parameters: {type: "addDepartamentos"},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('addDepartamentosButton'), "click", AddDepartamentos);
			Event.observe($('fviewclose'), "click", function(){ AddDepartamentosDiv(0); });
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddDepartamentos()
{
	new Ajax.Request(WEB_ROOT+'/ajax/departamentos.php',
	{
		method:'post',
		parameters: $('addDepartamentosForm').serialize(true),
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
				AddDepartamentosDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

