Event.observe(window, 'load', function() 
{
	if($('rfc'))
	{
        var time_id =  -1;
        var field_value = '';
        Event.observe($('rfc'), "keyup", function(){
            field_value =  this.value;
            clearTimeout(time_id);
            if(field_value.length>=3){
                time_id =  setTimeout(function () {
                    SuggestUser();
                },350)
            };
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

	if($('divForm')!= undefined)
	{
		$('divForm').observe("click", AddSuggestListener);
	}
		
});

function GoToWorkflow(path, id)
{
	if($('responsableCuenta'))
	{
		var responsableCuenta = $('responsableCuenta').value;
	}
	else
	{
		var responsableCuenta = 0;
	}
	new Ajax.Request(WEB_ROOT+'/ajax/report-obligaciones.php', 
	{
  	parameters: {value: id, type: "goToWorkflow", path: path, rfc: $('rfc').value, responsableCuenta: responsableCuenta, year: $('year').value, from: "report-servicios"},
		method:'post',
    onSuccess: function(transport){
      var response = transport.responseText || "no response text";
			window.open(WEB_ROOT+"/workflow/id/"+id, '_blank');
			//window.location = 
		},
    onFailure: function(){ alert('Something went wrong...') }
  });
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

function FillRFC(elem, id)
{
	$('suggestionDiv').hide();
	FillDatos(id);
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

function ShowClienteTable(id)
{
	if(!$('cliente-'+id).visible())
	{
		$('cliente-'+id).show();
		$('showCliente-'+id).innerHTML = "[-]";
	}
	else
	{
		$('cliente-'+id).hide();
		$('showCliente-'+id).innerHTML = "[+]";
	}
}

function ShowContractTable(id)
{
	if(!$('contract-'+id).visible())
	{
		$('contract-'+id).show();
		$('showContract-'+id).innerHTML = "[-]";
	}
	else
	{
		$('contract-'+id).hide();
		$('showContract-'+id).innerHTML = "[+]";
	}
}

function ViewItems(id){
	
	var obj = $('item_'+id);
	var color = $('color_'+id);
	if(obj.style.display == "none"){
		color.style.color = "RED";
		obj.style.display = "";
	}
	else{
		color.style.color = "blue";
		obj.style.display = "none";
	}
}

function doSearch(){
	$('type').value = "search";

	new Ajax.Request(WEB_ROOT+'/ajax/report-cxc.php',
	{
		method:'post',
		parameters: $('frmSearch').serialize(true),
		onLoading: function(){
			$("loading").style.display = "block";
			$('contenido').innerHTML = "";
		},
		onSuccess: function(transport){		
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");
			
			$("loading").style.display = "none";
			$('contenido').innerHTML = response;
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
	
}
function ExportReporteCxc()
{
    var resp = confirm("Esta seguro de generar este reporte? El proceso puede tardar varios minutos.");

    if(!resp)
        return;


    $('frmSearch').submit(); return true;
}



