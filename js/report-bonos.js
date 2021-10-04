function printExcelBonos(type) {

	var str = $('frmSearch').serialize();
	var datos = str.replace("type=search","type="+type);

	new Ajax.Request(WEB_ROOT+'/ajax/report-bonos-exel-pdf.php',
	{
		parameters: datos,
		method:'post',
		onLoading: function(){
			$('loadPrint').innerHTML = "Sea paciente mientras carga el archivo...";
		},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			console.log(response);
			var splitResp = response.split("[#]");

			$('loadPrint').innerHTML = "";

			if(splitResp[0] == "ok"){
				window.location = splitResp[1];
			}else{
				$('loadPrint').innerHTML = response;

			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

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
function doSearch() {
	ExportRepServBono();
}
function ExportRepServBono()
{
    var resp = confirm("Esta seguro de generar este reporte? El proceso puede tardar varios minutos.");
    if(!resp)
        return;
	jQ.ajax({
		url:WEB_ROOT+'/ajax/report-bonos.php',
		type:'post',
		data:jQ('#frmSearch').serialize(true),
		beforeSend: function () {
			jQ('#loading-img').show();
			jQ('#btnBuscar').hide();
		},
		success:function (response) {
			var splitResp = response.split("[#]");
			if(splitResp[0]=='ok')
			{
				jQ('#loading-img').hide();
				jQ("#btnBuscar").show();
				window.location = splitResp[1];
			}else{
				jQ('#loading-img').hide();
				jQ("#btnBuscar").show();
			}
		},
		error:function () {
			alert("Error al mostrar informacion!!");
		}
	});
}
jQ(document).on('click','.detailCobranza',function (e) {
    e.preventDefault();
    var datos =  jQ(this).data('datos');
    jQ.ajax({
        url:WEB_ROOT+'/ajax/workflow.php',
        type:'post',
        data:{type:'viewFacturas',datos:JSON.stringify(datos)},
        success:function (response) {
            grayOut(true);
            $('fview').show();
            FViewOffSet(response);
        },
        error:function () {
            alert("Error al mostrar informacion!!");

        }
    });
});
