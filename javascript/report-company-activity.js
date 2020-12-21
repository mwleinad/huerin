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
			var splitResp = response.split("[#]");
			if(splitResp[0]==='ok')
			{
				jQ('#loading').hide();
				jQ("#btnBuscar").show();
				window.location = splitResp[1];
			}else{
				jQ('#loading').hide();
				jQ("#btnBuscar").show();
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});

}
