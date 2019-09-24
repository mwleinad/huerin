Event.observe(window, 'load', function() 
{
	if($('rfc2'))
	{
		var time_id2 =  -1;
		var field_value2 = '';
		Event.observe($('rfc2'), "keyup", function(e){
			field_value =  this.value;
			clearTimeout(time_id);
			if(field_value2.length>=3){
				time_id2 =  setTimeout(function () {
					SuggestUser2();
				},350)
			}
		});
	}
	if($('rfc'))
	{
		var time_id =  -1;
		var field_value = '';
		Event.observe($('rfc'), "keyup", function(e){
			field_value =  this.value;
			clearTimeout(time_id);
			if(field_value.length>=3){
				time_id =  setTimeout(function () {
					SuggestUser();
				},350)
			}
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

		del = el.hasClassName('closeSuggestUserDiv');
		if(del == true){
			$('suggestionDiv').hide();
			return;
		}
		
	}

		
		AddSuggestListener2 = function(e) {
		var el = e.element();
		var del = el.hasClassName('suggestUserDiv');
		var id = el.identify();
		if(del == true){
			FillRFC2(1, id);
			return;
		}

		del = el.hasClassName('closeSuggestUserDiv');
		if(del == true){
			$('suggestionDiv2').hide();
			return;
		}
		
	}

	if($('suggestionDiv')!= undefined)
	{
		$('suggestionDiv').observe("click", AddSuggestListener);
	}	
	if($('suggestionDiv2')!= undefined)
	{
		$('suggestionDiv2').observe("click", AddSuggestListener2);
	}	
});

function FillRFC(elem, id)
{
	$('suggestionDiv').hide();
	FillDatos(id);
}

function FillRFC2(elem, id)
{
	$('suggestionDiv2').hide();
	FillDatos2(id);
}

function FillDatos(id)
{
	$('loadingDivDatosFactura').innerHTML = '<img src="'+WEB_ROOT+'/images/load.gif" />';
//	$('suggestionDiv').hide();
	new Ajax.Request(WEB_ROOT+'/ajax/fill_form_report.php', 
	{
  	parameters: {value: id, type: "datos"},
		method:'post',
    onSuccess: function(transport){
      var response = transport.responseText || "no response text";
			var splitResponse = response.split("{#}");
			$('rfc').value = splitResponse[0];
			$('cliente').value = id;
			$('loadingDivDatosFactura').innerHTML = '';
			//BuscarServiciosActivos();
		},
    onFailure: function(){ alert('Something went wrong...') }
  });
}

function FillDatos2(id)
{
	$('loadingDivDatosFactura2').innerHTML = '<img src="'+WEB_ROOT+'/images/load.gif" />';

//	$('suggestionDiv').hide();
	new Ajax.Request(WEB_ROOT+'/ajax/fill_form_servicios.php', 
	{
  	parameters: {value: id, type: "datos"},
		method:'post',
    onSuccess: function(transport){
      var response = transport.responseText || "no response text";
			var splitResponse = response.split("{#}");
			$('rfc2').value = splitResponse[0];
			$('loadingDivDatosFactura2').innerHTML = '';
			//BuscarServiciosActivos();
		},
    onFailure: function(){ alert('Something went wrong...') }
  });
}

function SuggestUser()
{
	new Ajax.Request(WEB_ROOT+'/ajax/suggest_report.php', 
	{
  	parameters: {value: $('rfc').value},
		method:'post',
    onSuccess: function(transport){
      var response = transport.responseText || "no response text";
			$('suggestionDiv').show();
			$('suggestionDiv').innerHTML = response;
			AddSuggestListener();
		},
    onFailure: function(){ alert('Something went wrong...') }
  });
}

function SuggestUser2()
{
	new Ajax.Request(WEB_ROOT+'/ajax/suggest_customer.php', 
	{
  	parameters: {value: $('rfc2').value},
		method:'post',
    onSuccess: function(transport){
      var response = transport.responseText || "no response text";
			$('suggestionDiv2').show();
			$('suggestionDiv2').innerHTML = response;
			AddSuggestListener2();
		},
    onFailure: function(){ alert('Something went wrong...') }
  });
}

function doSearch(){
	$('type').value = "search";

	new Ajax.Request(WEB_ROOT+'/ajax/report-documentacion-permanente.php',
	{
		method:'post',
		parameters: $('frmSearch').serialize(true),
		onLoading: function(){
			$("loading").style.display = "block";
			$('contenido').innerHTML = "";
		},
		onSuccess: function(transport){
			
			$("loading").style.display = "none";
			
			var response = transport.responseText || "no response text";
			//alert(response);
			var splitResponse = response.split("[#]");
//			if(splitResponse[0] == "ok")			
//			{				
				$('contenido').innerHTML = response;
//			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
	
}
