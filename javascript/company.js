var tableCompany = function () {
    var customColumns = [
        {"title": "Nombre", "data": "name"},
        {"title": "Rfc", "data": "taxpayer_id"},
        {"title": "Representante legal", "data": "legal_representative"},
        {"title": "Paso", "data": "step_name"},
        {"title": "Observaciones", "data": "comment"},
        {"title": "", "data": null},
    ]
    var handleTable = function () {
        var grid = new Datatable();
        var predicates = [];
        predicates.push({ name:"prospect_id", comparison:"array", value:[parseInt(jQ('#prospect_id').val())]})
        grid.init({
            src: jQ("#box-table-a"),
            onSuccess: function (grid, response) {
                // grid:        grid object
                // response:    json object of server side ajax response
                // execute some code after table records loaded
            },
            onError: function (grid) {
                // execute some code on network or other general error
            },
            onDataLoad: function(grid) {
                // execute some code on ajax data load
            },
            loadingMessage: 'Cargando...',
            dataTable: {
                "bStateSave": true,
                "columns": customColumns,
                "columnDefs": [
                    {
                        "targets": -1,
                        "render": function (data) {
                            var content = '<div class="center">';
                            content = content +  '<a href="javascript:;" title="Editar empresa" data-id="'+data.id+'" data-type="openEditCompany" class="spanControlCompany"><img src="'+WEB_ROOT+'/images/icons/edit.gif" aria-hidden="true" /></a>';
                            content = content +  '<a href="'+data.prospect.url+'" title="Resolver encuesta" target="_blank"><img src="'+WEB_ROOT+'/images/icons/task.png" aria-hidden="true" /></a>'
                            if(data.step_id === 2)
                                content = content +  '<a href="javascript:;" title="Generar cotizacion" data-id="'+data.id+'" data-type="generarCotizacion" class="spanControlCompany"><img src="'+WEB_ROOT+'/images/icons/update_payments.png" aria-hidden="true" /></a>'
                            content = content + '</div>';
                            return content;
                        }
                    }
                ],
                "lengthMenu": [
                    [10, 20, 50, 100, 150, -1],
                    [10, 20, 50, 100, 150, "All"] // change per page values here
                ],
                "pageLength": 10, // default record count per page
                "ajax": {
                    "type":'GET',
                    "url": URL_API + '/company', // ajax source
                    "contentType":'application/json',
                    "data": { predicates: predicates },
                },
                "order": [
                    [1, "asc"]
                ],// set first column as a default sort by asc
                "language": {
                    "url":WEB_ROOT + '/properties/i18n/Spanish.json',
                },
            }
        });
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
        jQ(document).on("click", ".spanGenerate", function () {
            var form = jQ(this).parents('form:first');
            var object  = { id:1, selected_service:[1,2] }
            var jsonNormalize = JSON.stringify(object)
            console.log(jsonNormalize)
            //jsonNormalize = jsonNormalize.replace(/"\[/g, "[")
            //jsonNormalize = jsonNormalize.replace(/]"/g, "]")
            jQ.ajax({
                url: URL_API + '/company/quote', // ajax source
                method: 'POST',
                contentType: 'application/json',
                data: jsonNormalize,
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization', driverApi.refreshToken())
                    jQ('.spanSaveGenerate').hide();
                    jQ('#loader').show();
                },
                success: function (response) {
                    console.log(response)
                }
            })
        });

        jQ(document).on('click', "#is_new_company", function () {
            jQ(this).is(':checked') ? jQ('#data_constitution').hide() : jQ('#data_constitution').show();
        })
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
                        grid.getDataTable().ajax.reload()
                        ShowStatusPopUp(splitResp[1]);
                        splitResp[0] === 'ok' && jQ('#fview').hide()
                    }
                });
            }
        });

    }
    return {
        init: function () {
            handleTable();
        }
    }
}();
jQ(document).ready(function () {
    tableCompany.init();
})



