	function Buscar(){
		
		$('type').value = "buscar";		
		
		new Ajax.Request(WEB_ROOT+'/ajax/cxc.php',{
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
	
function FillRFC(elem, id)
{
	$('suggestionDiv').hide();
	FillDatos(id);
}

function FillDatos(id)
{
		console.log(id);
	$('loadingDivDatosFactura').innerHTML = '<img src="'+WEB_ROOT+'/images/load.gif" />';

//	$('suggestionDiv').hide();
	new Ajax.Request(WEB_ROOT+'/ajax/fill_form_servicios.php', 
	{
  	parameters: {value: id, type: "datos"},
		method:'post',
    onSuccess: function(transport){
      var response = transport.responseText || "no response text";
			var splitResponse = response.split("{#}");
			if(response!="no response text")
			 $('nombre').value = splitResponse[0];
			else
			 $('nombre').value = "";
			
			//$('cuenta').value = id;
			$('loadingDivDatosFactura').innerHTML = '';
			//if(response!="no response text")
			//BuscarServiciosActivos();
		},
    onFailure: function(){ alert('Something went wrong...') }
  });
}

function SuggestUser()
{
	new Ajax.Request(WEB_ROOT+'/ajax/suggest_customer.php', 
	{
  	parameters: {value: $('nombre').value},
		method:'post',
    onSuccess: function(transport){
      var response = transport.responseText || "no response text";
			console.log(response);
			$('suggestionDiv').show();
			$('suggestionDiv').innerHTML = response;
			AddSuggestListener();
		},
    onFailure: function(){ alert('Something went wrong...') }
  });
}	
	
	Event.observe(window, 'load', function() {


	if($('nombre'))
	{
		Event.observe($('nombre'), "keyup", function(){ 
			SuggestUser(); 
			//FillDatosFacturacion();
		});
	}

	AddSuggestListener = function(e) {
		var el = e.element();
		var del = el.hasClassName('suggestUserDiv');
		var id = el.identify();
		if(del == true){
			FillRFC(1, id);
			return;
		}

	}

	if($('divForm')!= undefined)
	{
		$('divForm').observe("click", AddSuggestListener);
	}
																			 
		AddEditItemsListeners = function(e) {
			
			var el = e.element();
			var del = el.hasClassName('spanEdit');
			var efectivo = el.hasClassName('spanEfectivo');
			var id = el.identify();
			
			if(del == true)
			{
				EditCxCPopUp(id);
			}

			del = el.hasClassName('spanDetails');
						
			if(efectivo == true)
			{
				efec="ok";
			}
			else
			{
				efec="";
			}
			
			if(del == true)
			{

				PaymentDetails(id,efec);
			}

			del = el.hasClassName('spanAddPayment');
			
			if(del == true)
			{
				AddPaymentPopUp(id);
			}
			
		}

		AddDeletePaymentListeners = function(e) {
			
			var el = e.element();
			var del = el.hasClassName('spanDeletePayment');
			var id = el.identify();
			
			if(del == true)
			{
				DeletePayment(id);
			}

		}
		
	$('facturasListDiv').observe("click", AddEditItemsListeners);
	$('btnBuscar').observe("click", Buscar);
	
});

	function AddPaymentPopUp(id){
		
		grayOut(true);
		$('fview').show();
		if(id == 0){
			$('fview').hide();
			grayOut(false);
			return;
		}
		
		new Ajax.Request(WEB_ROOT+'/ajax/cxc.php',{
			method:'post',
			parameters: {type: "addPayment", id:id},
			onSuccess: function(transport){
				var response = transport.responseText || "no response text";
				FViewOffSet(response);
				Event.observe($('closePopUpDiv'), "click", function(){ PaymentDetails(0); });
				$('addPaymentButton').observe("click", AddPayment);
	
			},
		onFailure: function(){ alert('Something went wrong...') }
	  });
	}//showDetailsPopup	

function AddPayment(id)
{
	var message = "Realmente deseas agregar un nuevo pago. Se actualizara el saldo";
	if(!confirm(message))
	{
		return;
	}
	
	new Ajax.Request(WEB_ROOT+'/ajax/cxc.php',
	{
		method:'post',
		parameters: {id:id, type:"saveAddPayment", folio:$('folio').value, folioA:$('folioA').value, nombre:$('nombre').value, mes:$('mes').value, anio:$('anio').value, status_activo:$('status_activo').value, metodoDePago:$('metodoDePago').value, amount:$('amount').value, comprobanteId:$('comprobanteId').value},
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
				$('mySaldoSpan').innerHTML = splitResponse[2];
				$('facturasListDiv').innerHTML = splitResponse[3];
				$('mySaldoFavorSpan').innerHTML = splitResponse[4];
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}


function DeletePayment(id)
{
	var message = "Realmente deseas eliminar este pago. Se actualizara el saldo";
	if(!confirm(message))
	{
		return;
	}
	
	new Ajax.Request(WEB_ROOT+'/ajax/cxc.php',
	{
		method:'post',
		parameters: {id:id, type:"deletePayment", folio:$('folio').value, folioA:$('folioA').value, nombre:$('nombre').value, mes:$('mes').value, anio:$('anio').value, status_activo:$('status_activo').value},
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
				$('myPaymentsDiv').innerHTML = splitResponse[2];
				$('mySaldoSpan').innerHTML = splitResponse[3];
				$('facturasListDiv').innerHTML = splitResponse[4];
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

	function PaymentDetails(id,efectivo){
		grayOut(true);
		$('fview').show();
		if(id == 0){
			$('fview').hide();
			grayOut(false);
			return;
		}
		
		new Ajax.Request(WEB_ROOT+'/ajax/cxc.php',{
			method:'post',
			parameters: {type: "paymentDetails", id:id,efectivo:efectivo},
			onSuccess: function(transport){
				var response = transport.responseText || "no response text";
				FViewOffSet(response);
				Event.observe($('closePopUpDiv'), "click", function(){ PaymentDetails(0); });
				$('fview').observe("click", AddDeletePaymentListeners);

				//$('editButton').observe("click", EditCxC);
	
			},
		onFailure: function(){ alert('Something went wrong...') }
	  });
		
	}//showDetailsPopup	

	function EditCxCPopUp(id){
		
		grayOut(true);
		$('fview').show();
		if(id == 0){
			$('fview').hide();
			grayOut(false);
			return;
		}
		
		new Ajax.Request(WEB_ROOT+'/ajax/cxc.php',{
			method:'post',
			parameters: {type: "editCxC", id:id},
			onSuccess: function(transport){
				var response = transport.responseText || "no response text";
				FViewOffSet(response);
				Event.observe($('closePopUpDiv'), "click", function(){ EditCxCPopUp(0); });
				$('editButton').observe("click", EditCxC);
	
			},
		onFailure: function(){ alert('Something went wrong...') }
	  });
		
	}//showDetailsPopup	
	
function EditCxC()
{
	new Ajax.Request(WEB_ROOT+'/ajax/cxc.php',
	{
		method:'post',
		parameters: {comprobanteId:$('comprobanteId').value, cxcDiscount:$('cxcDiscount').value, folio:$('folio').value, folioA:$('folioA').value, nombre:$('nombre').value, mes:$('mes').value, anio:$('anio').value, status_activo:$('status_activo').value, type:"saveEditCxC"},
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
				$('facturasListDiv').update(splitResponse[2]);
				grayOut(false);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}