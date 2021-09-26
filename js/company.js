var tableCompany = function () {
    var customColumns = [
        {"title": "Nombre", "data": "name"},
        {"title": "Rfc", "data": "taxpayer_id"},
        {"title": "Representante legal", "data": "legal_representative"},
        {"title": "Paso", "data": null},
        {"title": "Observaciones", "data": "comment"},
        {"title": "", "data": null},
    ]
    var handleTable = function () {
        var grid = new Datatable();
        var predicates = [];
        predicates.push({ name:"prospect_id", comparison:"array", value:[parseInt(jQ('#prospect_id').val())]})
        grid.init({
            src: jQ("#box-table-a"),
            onSuccess: function (grid, response) {},
            onError: function (grid) {},
            onDataLoad: function(grid) {},
            loadingMessage: 'Cargando...',
            dataTable: {
                "bStateSave": true,
                "columns": customColumns,
                "columnDefs": [
                    {
                        "targets": -1,
                        "render": function (data) {
                            const rgx = /^http:\/\/.*\/capture-info/g
                            var baseUrlAPi = URL_API.slice(0,-3)
                            var normalizeUrl= data.prospect.url.replace(rgx, baseUrlAPi + 'capture-info')
                            var content = "<div class='center'>";
                            content = content +  "<a href='javascript:;' title='Editar empresa' data-id='"
                                      + data.id +"' data-type='openEditCompany' class='spanControlCompany' style='margin: 2px'><img src='"
                                      + WEB_ROOT +"/images/icons/edit.gif' aria-hidden='true' /></a>";
                            content = content +  "<a href='javascript:;'  class='drawHistory' data-history='"+ JSON.stringify(data.step_trace) +"' title='Ver historial de movimientos' style='margin: 2px' ><img src='"
                                + WEB_ROOT + "/images/icons/history.png' aria-hidden='true' /></a>"
                            if(parseInt(data.step_id) !== 5)
                                content = content +  "<a href='" + normalizeUrl + "' title='Resolver encuesta' target='_blank' style='margin: 2px'><img src='"
                                      + WEB_ROOT +"/images/icons/task.png' aria-hidden='true' /></a>"
                            if(data.step_id >=2 && parseInt(data.step_id) !== 5)
                                content = content +  "<a href='javascript:;' title='Generar cotizacion' data-id='"
                                          + data.id +"' data-type='generarCotizacion' class='spanControlCompany' style='margin: 2px'><img src='"
                                          + WEB_ROOT +"/images/icons/update_payments.png' aria-hidden='true' /></a>"
                            if(data.step_id >=3) {
                                content = content + "<a href='javascript:;' title='Descargar cotizaciones' data-company='"
                                          + data.id + "' data-type='download_zip_quote' class='spanDownloadQuote' style='margin: 2px'><img src='"
                                          + WEB_ROOT + "/images/icons/zip.png' width='16' aria-hidden='true' /></a>"
                                if(data.step_id === 3)
                                    content = content + "<a href='javascript:;' title='Validar y/o ajustar cotizacion' data-company='"
                                          + data.id + "' data-type='openValidateQuote' class='spanOpenValidate' style='margin: 2px'><img src='"
                                          + WEB_ROOT + "/images/icons/check.png' aria-hidden='true' /></a>"
                            }
                            if(data.step_id === 4) {
                                content = content + "<a href='javascript:;' title='Aceptar o declinar cotizacion' data-company='"
                                    + data.id + "' data-type='openSendToMain' class='spanSendToMain' style='margin: 2px'><img src='"
                                    + WEB_ROOT + "/images/icons/action_check.gif' width='16' aria-hidden='true' /></a>"
                            }
                            content = content + "</div>";
                            return content;
                        }
                    },
                    {
                        targets: -3,
                        render: function (data) {
                            var history =  data.step_trace
                            var message_html = ""
                            history.sort((a,b) => b.id - a.id)
                            if(history.length > 0) {
                                if(parseInt(data.step_id) !== 5) {
                                    var expiration_date = history[0].expiration_date
                                    var current_date = new Date().toISOString().slice(0, 10)
                                    var date1 = new Date(current_date)
                                    var date2 = new Date(expiration_date)
                                    var vencido = (current_date === expiration_date || current_date > expiration_date) ?? false
                                    var diference = vencido ? (date1.getTime() - date2.getTime()) : (date2.getTime() - date1.getTime())
                                    var diference_in_days = diference / (1000 * 3600 * 24)
                                    message_html = vencido
                                        ? " ( <span style='color:red'>Vencido desde hace " + diference_in_days + " dias </span> )"
                                        : " ( <span style='color:darkgreen'>Vence en " + diference_in_days + " dias </span> )"
                                }
                            }
                            return data.step_name + message_html
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
                    generateSelectRegimen()
                    jQ(document).on('change', '#tax_purpose', function () {
                        jQ('.field_is_new_company').toggle(this.value === 'moral' ?? false)
                        jQ('#label_date_constitution').html( this.value === 'moral' ? 'Fecha constitucion' : 'Fecha de alta en el SAT')
                        jQ('#data_constitution').toggle(this.value !== '' ?? false)

                        jQ('.field_regimen').toggle(this.value !== '' ?? false)
                        jQ('#regimen_id').trigger('change');
                    })

                    if (jQ('#regimen_id').val() !== '')
                        jQ('#regimen_id').trigger('change');

                    if (jQ("#customMultiple").length) {
                        var select2Service = jQ("#customMultiple").select2 ({
                                placeholder: "Seleccionar un servicio.",
                                minimumResultsForSearch: 4,
                                formatSearching: 'Buscando opciones',
                                closeOnSelect: false,
                                multiple: true,
                                data: function () {
                                  return { results: response.listServices}
                                },
                        })
                        select2Service.val([response.services]).trigger('change')
                    }
                    if(document.getElementById('name')!=null)
                        pure_autocomplete(document.getElementById("name"), 'contract',
                            WEB_ROOT+"/ajax/pure-autocomplete.php",
                            ['rfc', 'regimen_id', 'activity_id', 'contract_exists', 'legal_representative', 'tax_purpose'], customer_id)

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
                        splitResp[0] === 'ok' && grid.getDataTable().ajax.reload()
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
            var data = { company_id: objectJson.id , comment: objectJson.comment}
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
        jQ(document).on('click', '.drawHistory', drawStepTrace)
        jQ(document).on('change', '.improvePrice', function () {
            var id = jQ(this).data('quote-id')
            var current_price =  jQ(this).data('initial-price')
            var amount =parseFloat(current_price * (this.value/100))
            var new_current_price = parseFloat(current_price) + amount
            jQ('#price_' + id).val(new_current_price)
        })
        jQ(document).on('click', '.spanSendToMain', function () {
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
                    if (jQ('.select2').length > 0) {
                        jQ('.select2').select2(ops);
                        new Select2Cascade(jQ('#sector'), jQ('#subsector'), WEB_ROOT + "/ajax/load_items_select.php", ops);
                        new Select2Cascade(jQ('#subsector'), jQ('#actividad_comercial'), WEB_ROOT + "/ajax/load_items_select.php", ops);
                    }
                    jQ(document).on('change', '#tax_purpose', function(){
                        jQ('.field_moral').toggle(this.value === 'moral' ?? false)
                    })
                    generateSelectRegimen()
                    if (jQ('#regimen_id').val() !== '')
                        jQ('#regimen_id').trigger('change')

                    setAccordionEffect();

                }
            })
        })
        jQ(document).on('click', '.spanSaveSendToMain', function () {
            var send =  confirm('Esta accion no se puede deshacer, esta seguro de realizarla.')
            if (!send)
                return

            var form = jQ(this).parents('form:first');
            if (form.length > 0) {
                jQ.ajax({
                    url: WEB_ROOT + '/ajax/company.php',
                    method: 'post',
                    data: form.serialize(true),
                    beforeSend: function () {
                        jQ('.spanSaveSendToMain').hide();
                        jQ('#loader').show();
                    },
                    success: function (response) {
                        jQ('#loader').hide();
                        jQ('.spanSaveSendToMain').show();
                        var splitResp = response.split("[#]");
                        ShowStatusPopUp(splitResp[1]);
                        grid.getDataTable().ajax.reload()
                        splitResp[0] === 'ok' && jQ('#fview').hide()
                    }
                });
            }
        });
        function generateSelectRegimen() {
            jQ('#regimen_id').select2({
                placeholder: 'Seleccione un regimen..',
                allowClear: true,
                minimumResultsForSearch: -1,
                formatSearching: 'Buscando opciones',
                ajax: {
                    type: 'post',
                    url: WEB_ROOT + "/ajax/load_items_select.php",
                    data: function () {
                        return {
                            type: 'regimen',
                            tax_purpose: jQ('#tax_purpose').val(),
                        }
                    },
                    processResults: function (data) {
                        return {results: data}
                    }
                },
                initSelection: function (element, callback) {
                    var id = jQ(element).val();
                    if (id !== '') {
                        jQ.post(WEB_ROOT + "/ajax/load_items_select.php", {
                            type: 'defaultRegimen',
                            tax_purpose: jQ('#tax_purpose').val(),
                            id: id
                        }, function (response) {
                            callback(response);
                        }, 'json');
                    }
                }
            });
        }
        function drawStepTrace () {
            grayOut(true);
            jQ('#fview').show();
            var history =  jQ(this).data('history')
            var tbody = "<tbody>";
            history.forEach(function (e) {
                tbody = tbody.concat("<tr>")
                tbody = tbody.concat("<td>" + e.step_name + "</td>")
                tbody = tbody.concat("<td>" + e.made_by + "</td>")
                tbody = tbody.concat("<td>" + e.comment + "</td>")
                tbody = tbody.concat("<td>" + moment(e.created_at).format('YYYY-MM-DD HH:mm:ss') + "</td>")
            })
            tbody += '</tbody>'
            var modal_html = "<div class='popupheader' style='z-index:70'>"
                +   "<div id='fviewmenu' style='z-index:70'>"
                +       "<div id='fviewclose'><span style='color:#CCC' id='closePopUpDiv' onClick='close_popup()'>close"
                +           "<img src='" + WEB_ROOT + "/images/b_disn.png' border='0' alt='close'/></span>"
                +       "</div>"
                +   "</div>"
                +   "<div id='ftitl'>"
                +       "<div class='flabel'>Ejemplo</div>"
                +       "<div id='vtitl'><span title='Titulo'>Ejemplo</span></div>"
                +   "</div>"
                +   "<div id='draganddrop' style='position:absolute;top:45px;left:640px'>"
                +       "<img src='" + WEB_ROOT + "/images/draganddrop.png' border='0' alt='mueve'/>"
                +   "</div>"
                +"</div>"
                +"<div class='wrapper'>"
                +"<table border='1' width='100%'>"
                +"<thead>"
                +"<tr><th>Proceso realizado</th><th>Realizado por</th><th>Comentarios</th><th>Fecha</th></tr>"
                +"</thead>"
                +tbody
                +"</table>"
                +"</div>"
            FViewOffSet(modal_html);
        }
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

    jQ(document).on('click', '.spanOpenValidate', function () {
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
