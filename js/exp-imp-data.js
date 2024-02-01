var AJAX_PATH = WEB_ROOT+'/ajax/import-data.php'

function descargarBitacora (id) {
    jQ.ajax({
        url: WEB_ROOT + '/ajax/bitacoraImportacion.php',
        data: { type: 4, id},
        type: 'POST',
        cache:false,
        success: function (response) {
            window.location = response
        }
    })
}
function cargarListaImportacion(page = 0) {
    jQ.ajax({
        url: WEB_ROOT + '/ajax/bitacoraImportacion.php',
        data: {type: 1, page},
        type: 'POST',
        beforeSend: function () {
            jQ('#content-bitacora').html("<p>Espere un momento...</p>");
            jQ('#content-bitacora').show();
        },
        success: function (response) {
            jQ('#content-bitacora').html(response);
        },
    });
}

function enviarRecotizacion () {
    var form = jQ(this).parents('form:first');
    jQ.ajax({
        url: WEB_ROOT + '/ajax/bitacoraImportacion.php',
        data: form.serialize(true),
        type: 'POST',
        beforeSend: function () {
            jQ('#btn-enviar-recotizacion').hide();
            jQ('#loading-img').show();
        },
        success: function (response) {
            var splitResp = response.split('[#]')
            jQ('#btn-enviar-recotizacion').show();
            jQ('#loading-img').hide();
            ShowStatusPopUp(splitResp[1]);
            close_popup();
        },
        error: function () {
            alert('error')
        }
    });
}
function openEnviarRecotizacion (id) {
    jQ.ajax({
        url: WEB_ROOT + '/ajax/bitacoraImportacion.php',
        data: {type: 2, id},
        type: 'POST',
        success: function (response) {
            grayOut(true);
            $('fview').show();
            FViewOffSet(response);
        },
    });
}
jQ(document).ready(function(){
    jQ(document).on('click', '#btn-enviar-recotizacion', enviarRecotizacion)
    jQ('#btnRun').on('click',function() {
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
            },
            success: function (response) {
                jQ('#' + id).show();
                var splitResp = response.split("[#]");
                if(splitResp[0]=='ok'){
                    jQ('#loading-img').hide();
                    ShowStatusPopUp(splitResp[1]);
                    cargarListaImportacion()
                }
                else{
                    jQ('#loading-img').hide();
                    ShowStatusPopUp(splitResp[1]);
                }
            },
            error: function () {
                jQ('#' + id).show();
                jQ('#loading-img').hide();
                form[0].reset();
                ShowErrorOnPopup("Error al importar",true)
            }
        });
    });
    if(jQ('#formato').length > 0) {
        jQ('#formato').change(function() {
            if(this.value === "")
                return;
           var value_split = this.value.split('#');
           var tipo = value_split[0];
           var status = value_split[1] !== "" ? value_split[1] : "activos";
           var url =  tipo === 'update_contract' || tipo === 'update_customer'
               ? WEB_ROOT +"/ajax/report-razon-social.php"
               : WEB_ROOT +"/ajax/exp-imp-layout.php";
           var type =  tipo === 'update_contract' || tipo === 'update_customer'
            ? "generate_report_razon_social"
            : "generate_layout";

           if(['layout-update-servicios','layout-recotizar-servicios','layout-reporte-recotizar','layout-inventario'].includes(tipo))
               type = tipo

           jQ.ajax({
              method:'post',
              url: url,
              data:{ type: type, tipo: tipo , type_report: tipo, tipos: status},
              beforeSend: function() {
                jQ("#loadPrint").show();
              },
              success:function(response) {
                  jQ("#loadPrint").hide();
                  window.location=response;
              } ,
              error:function () {
                  alert('Error al descargar layout');
              }
           });
        })
    }
    cargarListaImportacion()
});
