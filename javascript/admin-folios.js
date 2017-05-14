	function AgregarCertificado(){
		
		$('frmCertificado').submit();
		
	}//AgregarCertificado

	function CambiarRfcActivo(){
		
		new Ajax.Request(WEB_ROOT+'/ajax/sistema.php', 
		{
		parameters: {rfcId: $('rfcId').value, type: "cambiarRfcActivo"},
			method:'post',
		onSuccess: function(transport){
		  var response = transport.responseText || "no response text";
				window.location.reload();
			},
		onFailure: function(){ alert('Something went wrong...') }
	  });	
	
	}//CambiarRfcActivo
		
	function AddFoliosDiv(id){
		
		grayOut(true);
		if(id == 0){
			$('fview').hide();
			grayOut(false);
			return;
		}
		$('fview').show();
		
		new Ajax.Request(WEB_ROOT+'/ajax/manage-folios.php',{
			method:'post',
			parameters: {type: "addFolios"},
			onSuccess: function(transport){
				var response = transport.responseText || "no response text";
				FViewOffSet(response);
				Event.observe($('fviewclose'), "click", function(){ AddFoliosDiv(0); });
				Event.observe($('btnGuardarFolios'), "click", AddFolios);				
			},
		onFailure: function(){ alert('Something went wrong...') }
	  });
		
	}//AddFoliosDiv
	
	function AddFolios(){
		
		var form = $('frmAgregarFolios').serialize();
		new Ajax.Request(WEB_ROOT+'/ajax/manage-folios.php', 
		{
			method:'post',
			parameters: {form: form, type: "saveFolios"},
			onSuccess: function(transport){
				var response = transport.responseText || "no response text";
				
				var splitResponse = response.split("[#]");
				if(splitResponse[0] == "fail"){					
					ShowStatusPopUp(splitResponse[1])
				}else{
					ShowStatusPopUp(splitResponse[1])
					$('contenido').innerHTML = splitResponse[2];
					$('frmAgregarFolios').reset();					
					//reload event listeners here
				}
	
			},
		onFailure: function(){ alert('Something went wrong...') }
	  });
		
	}//AddFolios

	function guardar_folios(){
				
		var oOptions = {   
            method: "POST",   
            parameters: Form.serialize("frmAgregarFolios"),   
            asynchronous: true,   
            onFailure: function (oXHR) {				
                 $('txtMsg').update('<span class="txtRed">Error al guardar los datos.</span>');				 
            },   
            onLoading: function (oXHR) {				 
                 $('txtMsg').update('<img src="../images/loading.gif" border="0" /><span class="txtBlack">&nbsp;Enviando datos...</span>');   
            },                             
            onSuccess: function(oXHR) {
				 var message = oXHR.responseText;
				 
				 if(message == 'success'){
					$('frmAgregarFolios').reset();
					$('content').update('<p align="center">Los datos fueron guardados correctamente.</p>');				
				 }else
                 	$('txtMsg').update(oXHR.responseText);   
            }
       	};   

		var oRequest = new Ajax.Updater({success: oOptions.onSuccess.bindAsEventListener(oOptions)}, "../ajax/admin-folios.php", oOptions);
			
	}//guardar_folios
	
	function EditFoliosPopup(id){
		
		grayOut(true);
		$('fview').show();
		if(id == 0){
			$('fview').hide();
			grayOut(false);
			return;
		}
		
		new Ajax.Request(WEB_ROOT+'/ajax/manage-folios.php',{
			method:'post',
			parameters: {type: "editFolios", id_serie:id},
			onSuccess: function(transport){
				var response = transport.responseText || "no response text";
				FViewOffSet(response);
				Event.observe($('closePopUpDiv'), "click", function(){ AddFoliosDiv(0); });
				Event.observe($('btnEditarFolios'), "click", EditarFolios);
	
			},
		onFailure: function(){ alert('Something went wrong...') }
	  });
		
	}//EditFoliosPopup

	function DeleteFoliosPopup(id){
		
		var message = "Realmente deseas eliminar estos folios?";
		if(!confirm(message)){
			return;
		}	
	
		new Ajax.Request(WEB_ROOT+'/ajax/manage-folios.php',{
			method:'post',
			parameters: {type: "deleteFolios", id_serie: id},
			onSuccess: function(transport){
				var response = transport.responseText || "no response text";
				var splitResponse = response.split("[#]");
				ShowStatus(splitResponse[1])
				$('contenido').innerHTML = splitResponse[2];
			},
		onFailure: function(){ alert('Something went wrong...') }
	  });
		
	}//DeleteFoliosPopup
	
	function EditarFolios(){
		
		var form = $('frmEditarFolios').serialize();
		new Ajax.Request(WEB_ROOT+'/ajax/manage-folios.php', {
			method:'post',
			parameters: {form: form, type: "saveEditFolios"},
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
				}
	
			},
		onFailure: function(){ alert('Something went wrong...') }
	  });
		
	}//EditarFolios
	
	Event.observe(window, 'load', function() {
																			 
		AddEditFoliosListeners = function(e) {
			var el = e.element();
			var del = el.hasClassName('spanDelete');
			var id = el.identify();
			if(del == true){
				DeleteFoliosPopup(id);
				return;
			}
	
			del = el.hasClassName('spanEdit');
			if(del == true)
				EditFoliosPopup(id);
			
		}
	
	if($('contenido')!= undefined)
		$('contenido').observe("click", AddEditFoliosListeners);
		
	if($('agregarCertificado')!= undefined)
		Event.observe($('agregarCertificado'), "click", AgregarCertificado);

	Event.observe($('addFolios'), "click", function(){ AddFoliosDiv(1); });

});