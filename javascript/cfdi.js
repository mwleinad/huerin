	function Buscar(){
		
		$('type').value = "buscar";		
		
		new Ajax.Request(WEB_ROOT+'/ajax/cfdi.php',{
			method:'post',
			parameters: $('frmBusqueda').serialize(true),
			onLoading: function(){
				$('loadBusqueda').show();	
			},
			onSuccess: function(transport){								
				var response = transport.responseText || "no response text";				
				var splitResponse = response.split("[#]");
console.log(response);
				$('loadBusqueda').hide();

				if(splitResponse[0].trim()=="ok"){
					//$('total').update(splitResponse[1]);
					$('facturasListDiv').update(splitResponse[2]);
				}
			},
			onFailure: function(){ alert('Something went wrong...') }
	  	});	
		
	}//Buscar
	
