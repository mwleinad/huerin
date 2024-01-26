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
