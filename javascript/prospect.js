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
                            content = content +  '<a class="btn btn-xs yellow" href="'+WEB_ROOT+'/do-poll/id/' + data.victimaId +'"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>';
                            if (data.completePoll) {
                                content = content + '<a class="btn btn-xs green btn-chart" href="javascript:;" title="Ver grafica" id="' + data.victimaId + '"><i class="fa fa-bar-chart" aria-hidden="true"></i></a>';
                                content = content + '<a class="btn btn-xs green-dark" href="'+WEB_ROOT+'/poll-result-pdf/id/' + data.victimaId +'" title="Ver reporte" target="_blank"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>';
                            }
                            content = content + '<a class="btn btn-xs red btn-delete" href="javascript:;"' +'title="Eliminar" id="'+ data.victimaId +'"><i class="fa fa-minus-square" aria-hidden="true"></i></a>';
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
            }
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
                    jQ('#contenido').html(splitResp[2]);
                    jQ('#fview').hide();
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

// jquery datatable
// load rows via api ajax
