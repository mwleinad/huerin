var tableProspect = function () {
    var customColumns = [
        {"title": "Nombre", "data": "name"},
        {"title": "Telefono", "data": "phone"},
        {"title": "Email", "data": "email"},
        {"title": "Observaciones", "data": "comment"},
        {"title": "", "data": null},
    ]
    var handleProspect = function () {
        var grid = new Datatable();
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
            dataTable: { // here you can define a typical datatable settings from http://datatables.net/usage/options

                // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
                // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/scripts/datatable.js).
                //"dom": "<'grid_16'r<'grid_12't><'grid_6'pli>>",
                // So when dropdowns used the scrollable div should be removed.
                // save datatable state(pagination, sort, etc) in cookie.
                "bStateSave": true,
                "columns": customColumns,
                "columnDefs": [
                    {
                        "targets": -1,
                        "render": function (data) {
                            var content = '<div class="center">';
                            content = content +  '<a href="javascript:;" title="Editar prospecto" data-id="'+data.id+'" data-type="openEditProspect" class="spanControlProspect"><img src="'+WEB_ROOT+'/images/icons/edit.gif" aria-hidden="true" /></a>';
                            content = content +  '<a href="'+WEB_ROOT+'/company/id/'+data.id+'" title="Ir a empresas" target="_blank"><img src="'+WEB_ROOT+'/images/icons/office-building.png" aria-hidden="true" /></a>';
                            content = content +  '<a href="'+data.url+'" title="Resolver encuesta" target="_blank"><img src="'+WEB_ROOT+'/images/icons/task.png" aria-hidden="true" /></a>'
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
                    "url": URL_API + '/prospect', // ajax source
                },
                "order": [
                    [1, "asc"]
                ],// set first column as a default sort by asc
                "language": {
                    "url":WEB_ROOT + '/properties/i18n/Spanish.json',
                },
            }
        });

        jQ(document).on("click", ".spanControlProspect", function () {
            var type = jQ(this).data('type');
            var id = jQ(this).data('id');
            jQ.ajax({
                url: WEB_ROOT + "/ajax/prospect.php",
                type: 'post',
                data: {type: type, id: id},
                success: function (response) {
                    grayOut(true);
                    jQ('#fview').show();
                    FViewOffSet(response);
                    if(document.getElementById('name')!=null)
                        pure_autocomplete(document.getElementById("name"), 'customer',
                            WEB_ROOT+"/ajax/pure-autocomplete.php",
                            ['phone', 'email', 'observation', 'customer_exists'])
                },
                error: function () {
                    alert("Error");
                }
            });
        });

        jQ(document).on('click', '.spanSaveProspect', function () {
            var form = jQ(this).parents('form:first');
            if (form.length > 0) {
                jQ.ajax({
                    url: WEB_ROOT + '/ajax/prospect.php',
                    method: 'post',
                    data: form.serialize(true),
                    beforeSend: function () {
                        jQ('.spanSaveProspect').hide();
                        jQ('#loader').show();
                    },
                    success: function (response) {
                        var splitResp = response.split("[#]");
                        if (splitResp[0] == 'ok') {
                            jQ('#loader').hide();
                            jQ('.spanSaveProspect').show();
                            ShowStatusPopUp(splitResp[1]);
                            jQ('#fview').hide();
                            grid.getDataTable().ajax.reload()

                        } else {
                            ShowStatusPopUp(splitResp[1]);
                            jQ('#loader').hide();
                            jQ('.spanSaveProspect').show();
                        }
                    }
                });
            } else
                return;
        });
    }
    return {
        init: function () {
            handleProspect();
        }
    }
}();

jQ(document).ready(function () {
    tableProspect.init();
})
