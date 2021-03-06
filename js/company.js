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
                            content = content +  '<a href="javascript:;" title="Editar empresa" data-id="'
                                      + data.id +'" data-type="openEditCompany" class="spanControlCompany"><img src="'
                                      + WEB_ROOT +'/images/icons/edit.gif" aria-hidden="true" /></a>';
                            content = content +  '<a href="'+data.prospect.url+'" title="Resolver encuesta" target="_blank"><img src="'
                                      + WEB_ROOT +'/images/icons/task.png" aria-hidden="true" /></a>'
                            if(data.step_id >=2)
                                content = content +  '<a href="javascript:;" title="Generar cotizacion" data-id="'
                                          + data.id +'" data-type="generarCotizacion" class="spanControlCompany"><img src="'
                                          + WEB_ROOT +'/images/icons/update_payments.png" aria-hidden="true" /></a>'
                            if(data.step_id >=3) {
                                content = content + '<a href="javascript:;" title="Descargar cotizaciones" data-company="'
                                          + data.id + '" data-type="download_zip_quote" class="spanDownloadQuote"><img src="'
                                          + WEB_ROOT + '/images/icons/zip.png" width="16" aria-hidden="true" /></a>'
                                if(data.step_id === 3)
                                    content = content + '<a href="javascript:;" title="Validar y/o ajustar cotizacion" data-company="'
                                          + data.id + '" data-type="openValidateQuote" class="spanOpenValidate"><img src="'
                                          + WEB_ROOT + '/images/icons/check.png" aria-hidden="true" /></a>'
                            }
                            if(data.step_id === 4) {
                                content = content + '<a href="javascript:;" title="Aceptar o declinar cotizacion" data-company="'
                                    + data.id + '" data-type="download_zip_quote" class="spanCloseProspect"><img src="'
                                    + WEB_ROOT + '/images/icons/action_check.gif" width="16" aria-hidden="true" /></a>'
                            }
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
                    "url": URL_API + '/company', // ajax source
                    "type":'GET',
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
            var customer_id = document.getElementById('customer').value;
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
                    if(document.getElementById('name')!=null)
                        pure_autocomplete(document.getElementById("name"), 'contract',
                            WEB_ROOT+"/ajax/pure-autocomplete.php",
                            ['rfc', 'regimen_id', 'activity_id', 'contract_exists'], customer_id)
                },
                error: function () {
                    alert("Error");
                }
            });
        });
        jQ(document).on("click", ".spanGenerate", function () {
            var form = jQ(this).parents('form:first');
            var jsonObject = jQ(form[0]).convertFormToJson();
            var jsonSerializado = JSON.stringify(jsonObject);
            jQuery.ajax({
                url: URL_API + '/company/quote', // ajax source
                type: 'POST',
                contentType: 'application/json',
                data: jsonSerializado,
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization', driverApi.refreshToken())
                    jQ('.spanGenerate').hide();
                    jQ('#loader').show();
                },
                success: function (response) {
                    ShowErrorOnPopup(response.message)
                    grid.getDataTable().ajax.reload()
                    jQ('.spanGenerate').show();
                    jQ('#loader').hide();
                    close_popup();
                },
                error: function (error) {
                    ShowErrorOnPopup(error.responseJSON.message, true);
                    jQ('.spanGenerate').show();
                    jQ('#loader').hide();
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

        jQ(document).on("click", ".spanSendValidate", function () {
            var form = jQ(this).parents('form:first');
            var jsonObject = jQ(form[0]).convertFormToJson();
            var jsonSerializado = JSON.stringify(jsonObject);
            var objectJson = JSON.parse(jsonSerializado);
            var data = { company_id: objectJson.id }
            if(objectJson.list_quotes.length > 0) {
                var quotes = [];
                objectJson.list_quotes.forEach(function (res) {
                    var quote = {
                        id: parseInt(res),
                        total: parseInt(objectJson['price_'+res]),
                        status: 1,
                    }
                    quotes.push(quote)
                })
                data.quotes = quotes
            }
            jQuery.ajax({
                url: URL_API + '/company/validate_quote', // ajax source
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(data),
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization', driverApi.refreshToken())
                    jQ('.spanSendValidate').hide();
                    jQ('#loader').show();
                },
                success: function (response) {
                    ShowErrorOnPopup(response.message)
                    grid.getDataTable().ajax.reload()
                    jQ('.spanSendValidate').show();
                    jQ('#loader').hide();
                    close_popup();
                },
                error: function (error) {
                    ShowErrorOnPopup(error.responseJSON.message, true);
                    jQ('.spanSendValidate').show();
                    jQ('#loader').hide();
                }
            })
        });

        jQ(document).on('click', '.spanUnlockPrice', function () {
            jQ('.inputPrice').prop('readonly', false)
        })
    }
    return {
        init: function () {
            handleTable();
        }
    }
}();
jQ(document).ready(function () {
    if(window.Prototype) {
        delete Array.prototype.toJSON;
    }
    tableCompany.init();
    jQ(document).on('click', '.spanDownloadQuote', function () {
        var _formJson = {
            id: jQ(this).data('company'),
            service_id: jQ(this).data('service'),
            quote_id: jQ(this).data('quote')
        }
        var type = jQ(this).data('type');
        var url_section = type === 'download_zip_quote' ? 'download_zip_quote' : 'download_quote'
        var download_ext = type === 'download_zip_quote' ? 'zip' : 'docx'

        jQ.ajax({
            url: URL_API + '/company/' + url_section, // ajax source
            type: 'POST',
            xhrFields: {
                responseType: 'blob'
            },
            contentType: 'application/json',
            data: JSON.stringify(_formJson),
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', driverApi.refreshToken())
                jQ('#loader').show();
            },
            success: function (response) {
                jQ('#loader').hide();
                const blob = new Blob([response], { type: 'application/*' })
                const link = document.createElement('a')
                link.href = window.URL.createObjectURL(blob)
                link.download = 'cotizacion.' + download_ext
                link.click()
            },
            error: function (error) {
                jQ('#loader').hide();
                ShowErrorOnPopup(error.statusText, true);
            }
        })
    })

    jQ(document).on('click', '.spanOpenValidate', function (e) {
        var id = jQ(this).data('company');
        var type= jQ(this).data('type');
        jQ.ajax({
            url: WEB_ROOT + "/ajax/company.php",
            type: 'post',
            data: { type, id },
            dataType:'json',
            success: function (response) {
                grayOut(true);
                jQ('#fview').show();
                FViewOffSet(response.template);
            }
        })
    })
})



