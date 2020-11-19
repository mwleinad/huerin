jQ(function() {
	jQ(document).on('click', '#btnBuscar', doSearch);
});
function doSearch(){
	new Ajax.Request(WEB_ROOT+'/ajax/report-company-activity.php',
	{
		method:'post',
		parameters: $('frmSearch').serialize(true),
		onLoading: function(){
			$("btnBuscar").style.display = "none";
			$("loading").style.display = "block";
			$('contenido').innerHTML = "";
		},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");
			$("btnBuscar").style.display = "block";
			$("loading").style.display = "none";
			$('contenido').innerHTML = splitResponse[1];
		},
		onFailure: function(){ alert('Something went wrong...') }
	});

}
