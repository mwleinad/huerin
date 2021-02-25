/***
 Wrapper/Helper Class for datagrid based on jQuery Datatable Plugin
 ***/
var Datatable = function() {

    var tableOptions; // main options
    var dataTable; // datatable object
    var table; // actual table jquery object
    var tableInitialized = false;
    var ajaxParams = {}; // set filter mode
    var the;

    return {
        init: function(options) {
            if (!jQ().dataTable) {
                return;
            }
            the = this;
            tableOptions = options;
            // default settings
            options = jQ.extend(true, {
                src: "", // actual table
                filterApplyAction: "filter",
                filterCancelAction: "filter_cancel",
                resetGroupActionInputOnSuccess: true,
                loadingMessage: 'Loading...',
                dataTable: {
                    "pageLength": 10, // default records per page
                    "language": { // language settings
                        // metronic spesific
                        "metronicGroupActions": "_TOTAL_ records selected:  ",
                        "metronicAjaxRequestGeneralError": "Could not complete request. Please check your internet connection",

                        // data tables spesific
                        "lengthMenu": "<span class='seperator'>|</span>View _MENU_ records",
                        "info": "<span class='seperator'>|</span>Found total _TOTAL_ records",
                        "infoEmpty": "No records found to show",
                        "emptyTable": "No data available in table",
                        "zeroRecords": "No se encontraron registros",

                        "paginate": {
                            "previous": "Prev",
                            "next": "Next",
                            "last": "Last",
                            "first": "First",
                            "page": "Page",
                            "pageOf": "of"
                        }
                    },

                    "orderCellsTop": true,
                    "columnDefs": [{ // define columns sorting options(by default all columns are sortable extept the first checkbox column)
                        'orderable': false,
                        'targets': [0]
                    }],

                     // pagination type(bootstrap, bootstrap_full_number or bootstrap_extended)
                    "autoWidth": false, // disable fixed width and enable fluid table
                    "processing": false, // enable/disable display message box on record load
                    "serverSide": true, // enable/disable server side ajax loading

                    "ajax": { // define ajax settings
                        "url": "", // ajax URL
                        "type": "get", // request type
                        "timeout": 20000,
                        "beforeSend": function (xhr) {
                            xhr.setRequestHeader("Authorization", driverApi.refreshToken());
                        },
                        "data": function(data) { // add request parameters before submit
                            jQ.each(ajaxParams, function(key, value) {
                                data[key] = value;
                            });
                        },
                        "dataSrc": function(res) { // Manipulate the data returned from the server
                            return res.payload;
                        },
                        "error": function() { // handle general connection errors
                            if (tableOptions.onError) {
                                tableOptions.onError.call(undefined, the);
                            }
                        }
                    },
                }
            }, options);
            
            table = jQ(options.src);
            // initialize a datatable
            dataTable = table.DataTable(options.dataTable);
        },
    };

};
