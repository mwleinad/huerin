Event.observe(window, 'load', function() {
	Event.observe($('addPersonal'), "click", AddPersonalDiv);

	AddEditPersonalListeners = function(e) {
		var el = e.element();
		var del = el.hasClassName('spanDelete');
		var id = el.identify();
		if(del == true)
		{
			DeletePersonalPopup(id);
			return;
		}

		del = el.hasClassName('spanEdit');
		if(del == true)
		{
			EditPersonalPopup(id);
		}
	}

	$('contenido').observe("click", AddEditPersonalListeners);

});

function EditPersonalPopup(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/personal.php',
	{
		method:'post',
		parameters: {type: "editPersonal", personalId:id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('closePopUpDiv'), "click", function(){ EditPersonalPopup(0); });
			Event.observe($('editPersonal'), "click", EditPersonal);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function EditPersonal()
{
	new Ajax.Request(WEB_ROOT+'/ajax/personal.php',
	{
		method:'post',
		parameters: $('editPersonalForm').serialize(true),
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
				AddPersonalDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function DeletePersonalPopup(id)
{
	var message = "Realmente deseas eliminar este contador?";
	if(!confirm(message))
	{
		return;
	}
	new Ajax.Request(WEB_ROOT+'/ajax/personal.php',
	{
		method:'post',
		parameters: {type: "deletePersonal", personalId: id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");
			ShowStatus(splitResponse[1])
			$('contenido').innerHTML = splitResponse[2];
				AddPersonalDiv(0);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddPersonalDiv(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/personal.php',
	{
		method:'post',
		parameters: {type: "addPersonal"},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('btnAddPersonal'), "click", AddPersonal);
			Event.observe($('fviewclose'), "click", function(){ AddPersonalDiv(0); });
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddPersonal()
{
	new Ajax.Request(WEB_ROOT+'/ajax/personal.php',
	{
		method:'post',
		parameters: $('addPersonalForm').serialize(true),
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
				AddPersonalDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

	function ToggleReporta()
	{
		if($('tipoPersonal').value == 'Auxiliar')
		{
			if($('jefeContadorDiv'))
				$('jefeContadorDiv').show();
			if($('jefeSupervisorDiv'))
				$('jefeSupervisorDiv').show();
			if($('jefeGerenteDiv'))
				$('jefeGerenteDiv').show();
			if($('jefeSocioDiv'))
				$('jefeSocioDiv').show();
		}

		if($('tipoPersonal').value == 'Contador')
		{
			if($('jefeContadorDiv'))
				$('jefeContadorDiv').hide();
			if($('jefeSupervisorDiv'))
				$('jefeSupervisorDiv').show();
			if($('jefeGerenteDiv'))
				$('jefeGerenteDiv').show();
			if($('jefeSocioDiv'))
				$('jefeSocioDiv').show();
		}

		if($('tipoPersonal').value == 'Supervisor')
		{
			if($('jefeContadorDiv'))
				$('jefeContadorDiv').hide();
			if($('jefeSupervisorDiv'))
				$('jefeSupervisorDiv').hide();
			if($('jefeGerenteDiv'))
				$('jefeGerenteDiv').show();
			if($('jefeSocioDiv'))
				$('jefeSocioDiv').show();
		}

		if($('tipoPersonal').value == 'Gerente')
		{
			if($('jefeContadorDiv'))
				$('jefeContadorDiv').hide();
			if($('jefeSupervisorDiv'))
				$('jefeSupervisorDiv').hide();
			if($('jefeGerenteDiv'))
				$('jefeGerenteDiv').hide();
			if($('jefeSocioDiv'))
				$('jefeSocioDiv').show();
		}

		if($('tipoPersonal').value == 'Socio')
		{
			if($('jefeContadorDiv'))
				$('jefeContadorDiv').hide();
			if($('jefeSupervisorDiv'))
				$('jefeSupervisorDiv').hide();
			if($('jefeGerenteDiv'))
				$('jefeGerenteDiv').hide();
			if($('jefeSocioDiv'))
				$('jefeSocioDiv').hide();
		}
		
		
	}

