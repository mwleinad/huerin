var tableQuestion = function () {
    var options = [];
    var current_option = resetCurrentOption()
    var customColumns = [
        {"title": "Pregunta", "data": "question"},
        {"title": "Servicio", "data": "service.name"},
        {"title": "", "data": null},
    ]
    var handleTable = function () {
        var grid = new Datatable();
        var predicates = [];
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
            onDataLoad: function (grid) {
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
                            content = content + '<a href="javascript:;" title="Editar pregunta" data-id="' + data.id + '" data-type="openEditQuestion" class="spanControlQuestion"><img src="' + WEB_ROOT + '/images/icons/edit.gif" aria-hidden="true" /></a>';
                            content = content + '<a href="javascript:;" title="Eliminar" data-id="' + data.id + '"  class="spanDeleteQuestion"><img src="' + WEB_ROOT + '/images/icons/delete.png" aria-hidden="true" /></a>';
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
                    "url": URL_API + '/question', // ajax source
                },
                "order": [
                    [1, "asc"]
                ],// set first column as a default sort by asc
                "language": {
                    "url": WEB_ROOT + '/properties/i18n/Spanish.json',
                },
            }
        });

        jQ(document).on("click", ".spanControlQuestion", function () {
            var type = jQ(this).data('type');
            var id = jQ(this).data('id');
            jQ.ajax({
                url: WEB_ROOT + "/ajax/question.php",
                type: 'post',
                data: {type: type, id: id},
                dataType: 'json',
                success: function (response) {
                    grayOut(true);
                    jQ('#fview').show();
                    FViewOffSet(response.template);
                    if ('info' in response && 'answer' in response.info)
                        options = response.info.answer
                },
                error: function () {
                    alert("Error");
                }
            });
        });
        jQ(document).on("click", ".spanControlOption", editOption)
        jQ(document).on("click", ".spanDeleteOption", deleteOption)
        jQ(document).on("click", ".spanAddOption", fnAddOption)
        jQ(document).on("click", ".spanSaveQuestion", function () {
            var form = jQ(this).parents('form:first');
            var jsonObject = jQ(form[0]).convertFormToJson();
            jsonObject = { ...jsonObject, answer:options}
            jsonSerializado = JSON.stringify(jsonObject)
            jQuery.ajax({
                url: URL_API + '/question', // ajax source
                type: jsonObject.id ? 'PATCH' : 'POST',
                contentType: 'application/json',
                data: jsonSerializado,
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization', driverApi.refreshToken())
                    jQ('.spanSaveQuestion').hide();
                    jQ('#loader').show();
                },
                success: function (response) {
                    jQ('#fview').hide();
                    FViewOffSet('');
                    ShowErrorOnPopup(response.message)
                    grid.getDataTable().ajax.reload()
                },
                error: function (error) {
                    jQ('.spanSaveQuestion').show();
                    ShowErrorOnPopup(error.responseJSON.message, true);
                }
            })
        })
        jQ(document).on("click", ".spanDeleteQuestion", function () {
            var id = jQ(this).data('id');
            var confirm =  window.confirm('¿Esta seguro de eliminar este registro?')
            if(!confirm)
                return

            jQ.ajax({
                url: URL_API + '/question', // ajax source
                type: 'DELETE',
                data: {id: id},
                dataType: 'json',
                success: function (response) {
                    ShowErrorOnPopup(response.message)
                    grid.getDataTable().ajax.reload()
                },
                error: function () {
                    alert("Error");
                }
            });
        });
    }

    function deleteOption () {
        var key =  jQ(this).data('key')
        jQ.ajax({
            url:WEB_ROOT + '/ajax/question.php',
            method:'POST',
            data: { key: key, type:'deleteOption'},
            dataType:'json',
            success: function (response) {
                options = response.options
                jQ('#listOption').html(response.template);
            }
        });
    }

    function editOption () {
        var key =  jQ(this).data('key')
        jQ.ajax({
           url:WEB_ROOT + '/ajax/question.php',
           method:'POST',
           data: { key: key, type:'editOption'},
           dataType:'json',
           success: function (response) {
               current_option = response
               current_option.key = key
               jQ('#optionText').val(response.text)
               jQ('#optionPrice').val(response.price);
           }
        });
    }

    function fnAddOption () {
        current_option.text = jQ('#optionText').val()
        current_option.price = jQ('#optionPrice').val()
        jQ.ajax({
            url:WEB_ROOT + '/ajax/question.php',
            method:'POST',
            data: { type:'addOption', ...current_option  },
            dataType: 'json',
            success: function (response) {
              if (response.status === 'ok') {
                  current_option = resetCurrentOption();
                  options = response.options
                  jQ('#optionText').val('')
                  jQ('#optionPrice').val('')
                  jQ('#listOption').html(response.template);
              } else  ShowStatusPopUp(response.message)
            }
        });
    }
    function resetCurrentOption() {
        return {id:null, text: null, price: null, answer: []}
    }
    return {
        init: function () {
            handleTable();
        }
    }
}();
jQ(document).ready(function () {
    if (window.Prototype) {
        delete Array.prototype.toJSON;
    }
    tableQuestion.init();
})



