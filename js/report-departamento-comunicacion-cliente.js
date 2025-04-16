function descargarReporte()
{
    var resp = confirm("Esta seguro de generar este reporte? El proceso puede tardar varios minutos.");
    if(!resp)
        return;

	jQ.ajax({
		url:WEB_ROOT+'/ajax/report-razon-social.php',
		type:'post',
		data:{ type:'generar_reporte_encargado_comunicacion_cliente'},
		beforeSend: function () {
			jQ('#loading-img').show();
			jQ('#btnDescargar').hide();
		},
		success:function (response) {
			jQ('#loading-img').hide();
			jQ("#btnDescargar").show();
			window.location = response;

		},
		error:function () {
			alert("Error al generar reporte!!");
		}
	});
}