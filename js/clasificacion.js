jQ(document).ready(function () {
    function saveOrUpdate () {
        var form = jQ(this).parents('form:first');
        jQ.ajax({
            url: WEB_ROOT + '/ajax/clasificacion.php',
            method: 'post',
            data: form.serialize(true),
            beforeSend: function () {
                jQ('.btn-control-clasificacion').hide();
                jQ('#loader').show();
            },
            success: function (response) {
                jQ('#loader').hide();
                jQ('.btn-control-clasificacion').show();
                var splitResp = response.split("[#]");
                splitResp[0] === 'ok' && jQ('#contenido').html(splitResp[2])
                ShowStatusPopUp(splitResp[1]);
                splitResp[0] === 'ok' && jQ('#fview').hide()
            }
        });
    }
    function eliminarClasificacion (id) {
        var message = "Esta seguro de eliminar este registro?";
        if (!confirm(message)) {
            return;
        }
        var id = jQ(this).data('id') ? parseInt(jQ(this).data('id')) : null;
        jQ.ajax({
            url: WEB_ROOT + '/ajax/clasificacion.php',
            method: 'post',
            data:  { type : 3, id },
            beforeSend: function () {
            },
            success: function (response) {
                var splitResp = response.split("[#]");
                splitResp[0] === 'ok' && jQ('#contenido').html(splitResp[2])
                ShowStatusPopUp(splitResp[1]);
            }
        });
    }
    jQ(document).on("click", ".span-control-clasificacion", function () {
        var type = parseInt(jQ(this).data('type'));
        var id = jQ(this).data('id') ? parseInt(jQ(this).data('id')) : null;
        jQ.ajax({
            url: WEB_ROOT + "/ajax/clasificacion.php",
            type: 'post',
            data: {type: type, id: id},
            dataType:'json',
            success: function (response) {
                grayOut(true);
                jQ('#fview').show();
                FViewOffSet(response.template);
                jQ('.btn-control-clasificacion').on('click', saveOrUpdate)
            },
            error: function () {
                alert("Error");
            }
        });
    });
    jQ(document).on("click", ".span-eliminar-clasificacion", eliminarClasificacion)
})
