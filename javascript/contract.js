var services = [];
jQ(document).ready(function () {
	var AddEditContractListeners = function () {
		var del = jQ(this).hasClass('spanAddService');
        var id = this.id;
		if(del==true){
            AddServicePopup(id);
 			return;
		}
        var del = jQ(this).hasClass('spanDelete');
        var id = this.id;
        if(del == true)
		{
			DeleteContractPopup(id);
			return;
		}
    }
    jQ("#contenido").on('click','*',AddEditContractListeners);
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
//block transferir contrato
jQ(document).on('click','.spanTransfer',function () {
	var id = this.id;
    jQ('#fview').show();
	jQ.ajax({
		url:WEB_ROOT+'/ajax/contract.php',
        type:'post',
		data:{type:'openModalTransfer',id:id},
		success:function (response) {
			console.log(response);
            FViewOffSet('');
            FViewOffSet(response);
            jQ('#closePopUpDiv').on('click',function(){
                close_popup();
            });
            jQ('#btnTransferContract').on('click',function(){
                doTransferContract();
            });
        },
		error:function(message){
			alert(message)
		}

	});
});
function doTransferContract(){
    jQ.ajax({
        url:WEB_ROOT+"/ajax/contract.php",
        data:jQ("#frmTransferContract").serialize(true),
        type: 'POST',
        beforeSend: function(){
            jQ('#btnTransferContract').hide();
            jQ('#loading-img').show();
        },
        success: function(response){
            var splitResp = response.split("[#]");
            if(splitResp[0]=='ok'){
                location.reload();
            }
            else{
                jQ('#btnTransferContract').show();
                jQ('#loading-img').hide();
                ShowStatusPopUp(splitResp[1]);
            }
        }
    });
}
//agregar multiple servicio
jQ(document).on('click','#addItemService',function () {
	var frm = jQ(this).parents('form:first');
	 var form = new FormData(frm[0]);
	 form.set('type','addItemService');
	jQ.ajax({
        url:WEB_ROOT+"/ajax/services.php",
        data:form,
        processData: false,
        contentType: false,
        type: 'POST',
        beforeSend: function(){
            jQ('#addItemService').hide();
        },
        success: function(response){
            var splitResp = response.split("[#]");
            if(splitResp[0]=='ok'){
                jQ('#contenidoItems').html(splitResp[2]);
                jQ('#addItemService').show();
                frm[0].reset();
            }
            else{
                jQ('#addItemService').show();
                ShowStatusPopUp(splitResp[1]);
            }
        }

	});

});
//delete multiple servicio
jQ(document).on('click','.spanDeleteItemService',function () {
    jQ.ajax({
        url:WEB_ROOT+"/ajax/services.php",
        data:{type:'delItemService',id:this.id},
        type: 'POST',
        success: function(response){
            var splitResp = response.split("[#]");
            jQ('#contenidoItems').html(splitResp[2]);
        }
    });
});

jQ(document).on('click','.spanUpdatePermisos',function () {
	var con= confirm('Esta seguro de actualizar los permisos.');
	if(!con)
		return;

    jQ.ajax({
        url: WEB_ROOT + "/ajax/contract.php",
        data: {type: 'doPermiso', contractId:jQ(this).data('id')},
        type: 'POST',
        success: function (response) {
            var splitResp = response.split("[#]");
            ShowStatusPopUp(splitResp[1]);
        }
       });
});

