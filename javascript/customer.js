Event.observe(window, 'load', function () {
    if ($('addCustomer')) {
        Event.observe($('addCustomer'), "click", AddCustomerDiv);
    }

    AddEditCustomerListeners = function (e) {
        var el = e.element();
        var del = el.hasClassName('spanDelete');
        var id = el.identify();
        if (del == true) {
            DeleteCustomerPopup(id);
            return;
        }
        del = el.hasClassName('spanEdit');
        if (del == true) {
            EditCustomerPopup(id);
        }
    }

    AddSuggestListener = function (e) {
        var el = e.element();
        var del = el.hasClassName('suggestUserDiv');
        var id = el.identify();
        if (del == true) {
            FillRFC(1, id);
            return;
        }
        del = el.hasClassName('closeSuggestUserDiv');
        if (del == true) {
            $('suggestionDiv').hide();
            return;
        }
    }

    $('contenido').observe("click", AddEditCustomerListeners);

    if ($('rfc')) {
        var time_id = -1;
        var field_value = '';
        Event.observe($('rfc'), "keyup", function () {
            field_value = this.value;
            clearTimeout(time_id);
            if (field_value.length >= 3) {
                time_id = setTimeout(function () {
                    SuggestUser();
                }, 350)
            }
        });
    }
    if ($('divForm') != undefined) {
        $('divForm').observe("click", AddSuggestListener);
    }

    Event.observe('frmCustomerSearch', 'submit', function (event) {
        Event.stop(event);
    });
});
function EliminarInactivos() {
    var message = "Realmente deseas eliminar los clientes inactivos? No podras revertir este proceso";
    if (!confirm(message)) {
        return;
    }
    window.location = WEB_ROOT + "/customer/delete/inactivos";
}
function Search() {
    if ($('rfc').value.length < 2) {
        return;
    }
    new Ajax.Request(WEB_ROOT + '/ajax/customer.php',
        {
            method: 'post',
            parameters: {valur: $('rfc').value, tipo: $('type').value, type: "search"},
            onSuccess: function (transport) {
                var response = transport.responseText || "no response text";
                $('contenido').innerHTML = response;
            },
            onFailure: function () {
                alert('Something went wrong...')
            }
        });
}
function SuggestUser() {
    var deep = $("deep").checked;
    var type = "subordinado";
    new Ajax.Request(WEB_ROOT + '/ajax/suggest_customer_catalog.php',
        {
            parameters: {
                value: $('rfc').value,
                tipo: $('type').value,
                type: type,
                responsableCuenta: $("responsableCuenta").value,
                seccion: "customer"
            },
            method: 'post',
            onSuccess: function (transport) {
                var response = transport.responseText || "no response text";
                $('suggestionDiv').show();
                $('suggestionDiv').innerHTML = response;
                AddSuggestListener();
            },
            onFailure: function () {
                alert('Something went wrong...')
            }
        });
}
function FillRFC(elem, id) {
    $('suggestionDiv').hide();
    FillDatos(id);
}
function FillDatos(id) {
    $('loadingDivDatosFactura').innerHTML = '<img src="' + WEB_ROOT + '/images/load.gif" />';
//	$('suggestionDiv').hide();
    new Ajax.Request(WEB_ROOT + '/ajax/fill_form_servicios_cliente_nocontract.php',
        {
            parameters: {value: id, type: "datos"},
            method: 'post',
            onSuccess: function (transport) {
                var response = transport.responseText || "no response text";
                var splitResponse = response.split("{#}");
                $('rfc').value = splitResponse[0];
                $('cuenta').value = id;
                $('loadingDivDatosFactura').innerHTML = '';
                BuscarServiciosActivos();
            },
            onFailure: function () {
                alert('Something went wrong...')
            }
        });
}
function BuscarServiciosActivos() {
    var deep = $("deep").checked;
    var respCuenta = $("responsableCuenta").value;
    var tipo = $('type').value;
    var rfc = $('rfc').value;
    if (deep)
        var type = "subordinado";
    else
        var type = "propio";
    new Ajax.Request(WEB_ROOT + '/ajax/customer.php',
        {
            method: 'post',
            parameters: {type: "search", valur: rfc, tipo: tipo, subor: type, responsableCuenta: respCuenta},
            onLoading: function () {
                $("loader").show();
            },
            onSuccess: function (transporta) {
                $("loader").hide();
                var respuesta = transporta.responseText;
                $('contenido').innerHTML = respuesta
            },
            onFailure: function () {
                alert('Se detecto un problema con el servidor');
            }
        });
}
function EditCustomerPopup(id) {
    grayOut(true);
    $('fview').show();
    if (id == 0) {
        $('fview').hide();
        grayOut(false);
        return;
    }
    new Ajax.Request(WEB_ROOT + '/ajax/customer.php',
        {
            method: 'post',
            parameters: {type: "editCustomer", customerId: id, valur: $('rfc').value, tipo: $('type').value},
            onSuccess: function (transport) {
                var response = transport.responseText || "no response text";
                FViewOffSet(response);
                Event.observe($('closePopUpDiv'), "click", function () {
                    EditCustomerPopup(0);
                });
                Event.observe($('editCustomer'), "click", EditCustomer);
            },
            onFailure: function () {
                alert('Something went wrong...')
            }
        });
}
function EditCustomer() {
    new Ajax.Request(WEB_ROOT + '/ajax/customer.php',
        {
            method: 'post',
            parameters: $('editCustomerForm').serialize(true),
			onLoading: function () {
				$("loader").style.display='block';
				document.getElementsByClassName("button_grey")[0].style.display = "none";
			},
            onSuccess: function (transport) {
				$("loader").style.display='none';
				document.getElementsByClassName("button_grey")[0].style.display = "block";
                var response = transport.responseText || "no response text";
                var splitResponse = response.split("[#]");
                if (splitResponse[0] == "fail") {
                    ShowStatusPopUp(splitResponse[1])
                } else {
                    ShowStatusPopUp(splitResponse[1])
                    $('contenido').innerHTML = splitResponse[2];
                    AddCustomerDiv(0);
                }
            },
            onFailure: function () {
                alert('Something went wrong...')
            }
        });
}
function DeleteCustomerPopup(id) {
    var message = "Realmente deseas cambiar el estatus de este cliente?";
    if (!confirm(message)) {
        return;
    }
    new Ajax.Request(WEB_ROOT + '/ajax/customer.php',
        {
            method: 'post',
            parameters: {type: "deleteCustomer", customerId: id, valur: $('rfc').value, tipo: $('type').value},
            onSuccess: function (transport) {
                var response = transport.responseText || "no response text";
                var splitResponse = response.split("[#]");
                ShowStatus(splitResponse[1])
                $('contenido').innerHTML = splitResponse[2];
                AddCustomerDiv(0);
            },
            onFailure: function () {
                alert('Something went wrong...')
            }
        });
}
function AddCustomerDiv(id) {
    grayOut(true);
    $('fview').show();
    if (id == 0) {
        $('fview').hide();
        grayOut(false);
        return;
    }
    new Ajax.Request(WEB_ROOT + '/ajax/customer.php',
        {
            method: 'post',
            parameters: {type: "addCustomer"},
            onSuccess: function (transport) {
                var response = transport.responseText || "no response text";
                FViewOffSet(response);
                Event.observe($('btnAddCustomer'), "click", AddCustomer);
                Event.observe($('fviewclose'), "click", function () {
                    AddCustomerDiv(0);
                });
            },
            onFailure: function () {
                alert('Something went wrong...')
            }
        });
}
function AddCustomer() {
    new Ajax.Request(WEB_ROOT + '/ajax/customer.php',
        {
            method: 'post',
            parameters: $('addCustomerForm').serialize(true),
            onLoading: function () {
                $("loader").style.display='block';
                document.getElementsByClassName("button_grey")[0].style.display = "none";
            },
            onSuccess: function (transport) {
				$("loader").style.display='none';
				document.getElementsByClassName("button_grey")[0].style.display = "block";
                var response = transport.responseText || "no response text";
                var splitResponse = response.split("[#]");
                if (splitResponse[0] == "fail") {
                    ShowStatusPopUp(splitResponse[1])
                } else {
                    ShowStatusPopUp(splitResponse[1])
                    $('contenido').innerHTML = splitResponse[2];
                    AddCustomerDiv(0);
                }
            },
            onFailure: function () {
                alert('Something went wrong...')
            }
        });
}
function ExportExcel() {
	$("addCustomerFormSearch").submit();
}
jQ(document).on('click', '.bajaTemporal', function (e) {
    e.preventDefault();
    jQ.ajax({
        url: WEB_ROOT + "/ajax/customer.php",
        type: 'post',
        data: {type: 'openModalBajaTemporal', id: this.id},
        success: function (response) {
            grayOut(true);
            $('fview').show();
            FViewOffSet(response);
        },
        error: function (error) {
        }
    });
})
jQ(document).on('click', '#btnDownServicio', function (e) {
    e.preventDefault();
    var form = jQ(this).parents('form:first');
    jQ.ajax({
        url: WEB_ROOT + "/ajax/customer.php",
        type: 'post',
        data: form.serialize(true),
        beforeSend: function () {
            jQ('#loading-img').show();
            jQ('#btnDownServicio').hide();
        },
        success: function (response) {
            var splitResp = response.split("[#]");
            if (splitResp[0] == 'ok') {
                close_popup();
                ShowStatusPopUp(splitResp[1])
                BuscarServiciosActivos();
            } else {
                jQ('#loading-img').hide();
                jQ('#btnDownServicio').show();
                ShowStatusPopUp(splitResp[1])
            }
        },
        error: function (error) {
            alert("Error!!!")
        }
    });
})
jQ(document).on('click', '.reactiveTemp', function (e) {
    e.preventDefault();
    jQ.ajax({
        url: WEB_ROOT + "/ajax/customer.php",
        type: 'post',
        data: {type: 'openModalReactiveTemporal', id: this.id},
        success: function (response) {
            grayOut(true);
            $('fview').show();
            FViewOffSet(response);
        },
        error: function (error) { }
    });
})
