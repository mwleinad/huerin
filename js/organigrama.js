function descargarOrganigrama()
{
    var resp = confirm("Esta seguro de generar este reporte? El proceso puede tardar varios minutos.");
    if(!resp)
        return;

	jQ.ajax({
		url:WEB_ROOT+'/ajax/organigrama.php',
		type:'post',
		data:jQ('#frmSearch').serialize(true),
		beforeSend: function () {
			jQ('#loading-img').show();
			jQ('#btnDescargar').hide();
		},
		success:function (response) {
			var splitResp = response.split("[#]");
			if(splitResp[0]=='ok')
			{
				jQ('#loading-img').hide();
				jQ("#btnDescargar").show();
				window.location = splitResp[1];
			}else{
				jQ('#loading-img').hide();
				jQ("#btnDescargar").show();
			}
		},
		error:function () {
			alert("Error al generar reporte!!");
		}
	});
}
