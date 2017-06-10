function doSearch(){
	
	new Ajax.Request(WEB_ROOT+'/ajax/report-cobranza.php',
	{
		method:'post',
		parameters: $('frmSearch').serialize(true),
		onLoading: function(){
			$("loading").style.display = "block";
			$("contenido").style.display = "none";
			$('totalRegs').innerHTML = "";
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
				$('totalRegs').innerHTML = splitResponse[2];
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
	
}