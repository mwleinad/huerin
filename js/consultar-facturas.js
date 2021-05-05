	function showDetailsPopup(id){
		
		grayOut(true);
		$('fview').show();
		if(id == 0){
			$('fview').hide();
			grayOut(false);
			return;
		}
		
		new Ajax.Request(WEB_ROOT+'/ajax/manage-facturas.php',{
			method:'post',
			parameters: {type: "showDetails", id_item:id},
			onSuccess: function(transport){
				var response = transport.responseText || "no response text";
				FViewOffSet(response);
				Event.observe($('closePopUpDiv'), "click", function(){ showDetailsPopup(0); });
				$('accionList').observe("click", SendItemsListeners);
	
			},
		onFailure: function(){ alert('Something went wrong...') }
	  });
		
	}//showDetailsPopup	

	function Exportar(){
		$('type').value = "exportar";		
		new Ajax.Request(WEB_ROOT+'/ajax/manage-facturas.php',{
			method:'post',
			parameters: $('frmBusqueda').serialize(true),
			onLoading: function(){
				$('loadBusqueda').show();	
			},
			onSuccess: function(transport){
				$('loadBusqueda').hide();
				
				var response = transport.responseText || "no response text";
				alert(response);
			},
			onFailure: function(){ alert('Something went wrong...') }
	  	});	
		
	}//Buscar
	
	function Buscar(){
		$('type').value = "buscar";		
				
		new Ajax.Request(WEB_ROOT+'/ajax/manage-facturas.php',{
			method:'post',
			parameters: $('frmBusqueda').serialize(true),
			onLoading: function(){
				$('loadBusqueda').show();	
			},
			onSuccess: function(transport){
				$('loadBusqueda').hide();
				
				var response = transport.responseText || "no response text";
				console.log(response);
				var splitResponse = response.split("[#]");
				if(splitResponse[0].trim() == "ok"){
					$('total').innerHTML = splitResponse[1];
					$('facturasListDiv').innerHTML = splitResponse[2];	
				}
			},
			onFailure: function(){ alert('Something went wrong...') }
	  	});	
		
	}//Buscar
	
	function EnviarEmail(id){
				
		new Ajax.Request(WEB_ROOT+'/ajax/manage-facturas.php', 
		{
			method:'post',
			parameters: {type: 'enviar_email', id_comprobante: id},
			onSuccess: function(transport){
				var response = transport.responseText || "no response text";
				var splitResponse = response.split("[#]");
				if(splitResponse[0] == "ok"){					
					ShowStatusPopUp(splitResponse[1])									
				}
        else {
          ShowStatusPopUp(splitResponse[1])
        } 
        
	
			},
		onFailure: function(){ alert('Something went wrong...') }
	  });
		
	}//EnviarEmail
	
	function CancelarFactura(id){
		
		grayOut(true);
		$('fview').show();
		if(id == 0){
			$('fview').hide();
			grayOut(false);
			return;
		}
		
		new Ajax.Request(WEB_ROOT+'/ajax/manage-facturas.php',{
			method:'post',
			parameters: {type: "cancelar_div", id_item:id},
			onSuccess: function(transport){
				var response = transport.responseText || "no response text";
				FViewOffSet(response);
				Event.observe($('closePopUpDiv'), "click", function(){ showDetailsPopup(0); });
				Event.observe($('btnCancelar'), "click", DoCancelacion);
	
			},
		onFailure: function(){ alert('Something went wrong...') }
	  });
		
	}//CancelarFactura
	
	function DoCancelacion(){
		
		new Ajax.Request(WEB_ROOT+'/ajax/manage-facturas.php', 
		{
			method:'post',
			parameters: $('frmCancelar').serialize(true),
			onLoading: function () {
				$('loading-img').style.display='block';
                $('btnCancelar').style.display='none';
            },
			onSuccess: function(transport){
				var response = transport.responseText || "no response text";
				var splitResponse = response.split("[#]");
				if(splitResponse[0] == "ok"){
                    $('total').innerHTML = splitResponse[2];
                    $('facturasListDiv').innerHTML = splitResponse[3];
                    $('loading-img').style.display='none';
                    $('btnCancelar').style.display='block';
                    $('frmCancelar').reset();
                    close_popup();
                    ShowStatusPopUp(splitResponse[1]);

				}else{
                    ShowStatusPopUp(splitResponse[1]);
                    $('loading-img').style.display='none';
                    $('btnCancelar').style.display='block';
				}
			},
		onFailure: function(){ alert('Something went wrong...') }
	  });
	}//DoCancelacion
	
	Event.observe(window, 'load', function() {
																			 
		AddEditItemsListeners = function(e) {
			
			var el = e.element();
			var del = el.hasClassName('spanDetails');
			var id = el.identify();
			
			if(del == true)
				showDetailsPopup(id);
			
			del = el.hasClassName('spanCancel');
			if(del == true)
				CancelarFactura(id);
			
		}
		
		SendItemsListeners = function(e) {
			
			var el = e.element();
			var del = el.hasClassName('spanSend');
			var id = el.identify();
			
//			alert(id);
			if(del == true)
				EnviarEmail(id);
			
		}
	
	$('facturasListDiv').observe("click", AddEditItemsListeners);
	$('btnBuscar').observe("click", Buscar);
        var element =  document.getElementById('btnExportar');
	if(typeof(element)!= 'undefined' && element != null)
		$('btnExportar').observe("click", Exportar);
	
	
	$('rfc').observe("keypress", function(evt) { 
       		if(evt.keyCode == 13)
				Buscar();
    }); 
	
	$('nombre').observe("keypress", function(evt) { 
       		if(evt.keyCode == 13)
				Buscar();
    }); 

	$('anio').observe("keypress", function(evt) { 
       		if(evt.keyCode == 13)
				Buscar();
    });


	
});