Event.observe(window, 'load', function() {
	//Event.observe($('addContract'), "click", AddContractDiv);
	AddEditContractListeners = function(e) {
		var el = e.element();
		var del = el.hasClassName('spanDelete');
		var id = el.identify();
		if(del == true)
		{
			DeleteContractPopup(id);
			return;
		}
		del = el.hasClassName('spanAddService');
		if(del == true)
		{
			AddServicePopup(id);
			return;
		}

	}

	$('contenido').observe("click", AddEditContractListeners);

});

function AddServicePopup(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}
	new Ajax.Request(WEB_ROOT+'/ajax/services.php',
	{
		method:'post',
		parameters: {type: "addServicio", id:id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('addServiceButton'), "click", AddService);
			Event.observe($('fviewclose'), "click", function(){ AddContractDiv(0); });
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}
function AddService()
{
	new Ajax.Request(WEB_ROOT+'/ajax/services.php',
	{
		method:'post',
		parameters: $('addServicioForm').serialize(true),
        onLoading:function() {
            $('loading-img').style.display='block';
            $('addServiceButton').style.display='none';
        },
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");
			if(splitResponse[0] == "fail")
			{
                $('loading-img').style.display='none';
                $('addServiceButton').style.display='block';
				ShowStatusPopUp(splitResponse[1])
			}
			else
			{
                $('loading-img').style.display='none';
                $('addServiceButton').style.display='block';
				ShowStatusPopUp(splitResponse[1])
				$('contenido').innerHTML = splitResponse[2];
				AddContractDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function EditContractPopup(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/contract.php',
	{
		method:'post',
		parameters: {type: "editContract", contractId:id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('closePopUpDiv'), "click", function(){ EditContractPopup(0); });
			Event.observe($('editContract'), "click", EditContract);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function DeleteContractPopup(id)
{
	var message = "Realmente deseas cambiar el estatus de esta razon social?";
	if(!confirm(message))
	{
		return;
	}
	new Ajax.Request(WEB_ROOT+'/ajax/contract.php',
	{
		method:'post',
		parameters: {type: "deleteContract", contractId: id, customer: $('customerId').value},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");
			ShowStatus(splitResponse[1])
			$('contenido').innerHTML = splitResponse[2];
			hideMessage();
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddContractDiv(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/contract.php',
	{
		method:'post',
		parameters: {type: "addContract"},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('btnAddContract'), "click", AddContract);
			Event.observe($('fviewclose'), "click", function(){ AddContractDiv(0); });
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddContract()
{
	new Ajax.Request(WEB_ROOT+'/ajax/contract.php',
	{
		method:'post',
		parameters: $('addContractForm').serialize(true),
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
				AddContractDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function hideMessage(){
	$("success").style.display = "none";	
}

function doSearch(){
	
	new Ajax.Request(WEB_ROOT+'/ajax/contract.php',
	{
		method:'post',
		parameters: $('frmSearch').serialize(true),
		onLoading: function(){
			$("loading").style.display = "block";
			$("contenido").style.display = "none";
		},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			
			var splitResponse = response.split("[#]");
			if(splitResponse[0] == "fail")
			{
				ShowStatusPopUp(splitResponse[1])
			}
			else
			{
				$("loading").style.display = "none";
				$("contenido").style.display = "block";
				$('contenido').innerHTML = splitResponse[1];
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
	
}

function UpdateCosto()
{
	new Ajax.Request(WEB_ROOT+'/ajax/services.php',
	{
		method:'post',
		parameters: {type:"updateCosto", id:$('tipoServicioId').value},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			$('costo').value = response;
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}