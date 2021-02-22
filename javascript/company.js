jQ(document).on("click", ".spanControlCompany", function () {
    var type = jQ(this).data('type');
    var id = jQ(this).data('id');
    var prospect_id = jQ(this).data('prospect');
    jQ.ajax({
        url: WEB_ROOT + "/ajax/company.php",
        type: 'post',
        data: {type: type, id: id, prospect_id},
        dataType:'json',
        success: function (response) {
            grayOut(true);
            jQ('#fview').show();
            FViewOffSet(response.template);
            if (jQ("#customMultiple").length) {
                jQ("select[multiple]").multiselect({
                    columns: 1,
                    search: true,
                    maxHeight: 40,
                    selectGroup: true,
                    selectAll:true,
                    texts: {
                        placeholder: 'Seleccionar servicios',
                        search         : 'Buscar',         // search input placeholder text
                        selectedOptions: ' Seleccionado',      // selected suffix text
                        selectAll      : 'Seleccionar todos',     // select all text
                        unselectAll    : 'Quitar todos',   // unselect all text
                        noneSelected   : 'Ningun elemento seleccionado'   // None selected text
                    }
                });
                jQ("select[multiple]").multiselect('loadOptions', response.services);
            }
        },
        error: function () {
            alert("Error");
        }
    });
});

jQ(document).on('click', '.spanSaveCompany', function () {
    var form = jQ(this).parents('form:first');
    if (form.length > 0) {
        jQ.ajax({
            url: WEB_ROOT + '/ajax/company.php',
            method: 'post',
            data: form.serialize(true),
            beforeSend: function () {
                jQ('.spanSaveCompany').hide();
                jQ('#loader').show();
            },
            success: function (response) {
                jQ('#loader').hide();
                jQ('.spanSaveCompany').show();
                var splitResp = response.split("[#]");
                if (splitResp[0] == 'ok') {
                    ShowStatusPopUp(splitResp[1]);
                    jQ('#contenido').html(splitResp[2]);
                    jQ('#fview').hide();
                } else {
                    ShowStatusPopUp(splitResp[1]);
                }
            }
        });
    } else
        return;
});
