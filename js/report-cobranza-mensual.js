var AJAX_PATH = WEB_ROOT+'/ajax/report-cobranza.php'
jQ(document).ready(function(){

    jQ('#btnSearch').on('click',function() {
        var id = this.id;
        var form = jQ(this).parents('form:first');
        var fd = new FormData(form[0]);
        jQ.ajax({
            url: AJAX_PATH,
            data: fd,
            processData: false,
            contentType: false,
            type: 'POST',
            beforeSend: function () {
                jQ('#loading-img').show();
                jQ('#' + id).hide();
                jQ('#contenido').html("<div style='text-align:center'><b>Este reporte puede tardar varios minutos si no eliges un cliente. Por favor sea paciente.</b></div>");
            },
            success: function (response) {
                var splitResp =  response.split("[#]");
                jQ('#' + id).show();
                jQ('#loading-img').hide();
                jQ('#contenido').html("<div style='width:500px;text-align:center;margin:0 auto;'><a href='"+splitResp[0]+"' title='Descargar excel'><img src='"+WEB_ROOT+"/images/excel.PNG' /></a><br>"+"<a href='"+splitResp[1]+"' title='Descargar zip'><img src='"+WEB_ROOT+"/images/icons/zip-icon.png' /></a></div>");
            },
            error: function () {
                jQ('#' + id).show();
                alert('error')
            }
        });
    });

});