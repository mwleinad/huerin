var DOC_ROOT = "../";
var DOC_ROOT_TRUE = "../";
var DOC_ROOT_SECTION = "../../";

function SuggestUser() {
    new Ajax.Request(WEB_ROOT + '/ajax/suggest.php',
        {
            parameters: {value: $('rfc').value, activos: true},
            method: 'post',
            onSuccess: function (transport) {
                var response = transport.responseText || "no response text";
                $('suggestionDiv').show();
                $('suggestionDiv').innerHTML = response;
                AddSuggestListenerInvoice();
            },
            onFailure: function () {
                alert('Something went wrong...')
            }
        });
}

function FillRFCInvoice(elem, id) {
    $('suggestionDiv').hide();
    FillDatosFacturacion(id);
}

function FillNoIdentificacion(elem, id) {
    $('noIdentificacion').value = id;
    $('suggestionProductDiv').hide();
    FillConceptoData();
}

function FillImpuestoId(elem, id) {
    $('impuestoId').value = id;
    $('suggestionImpuestoDiv').hide();
    FillImpuestoData();
}

function SuggestProduct() {
    new Ajax.Request(WEB_ROOT + '/ajax/suggest_x.php',
        {
            parameters: {value: $('noIdentificacion').value, type: "producto"},
            method: 'post',
            onSuccess: function (transport) {
                var response = transport.responseText || "no response text";
                $('suggestionProductDiv').show();
                $('suggestionProductDiv').innerHTML = response;
            },
            onFailure: function () {
                alert('Something went wrong...')
            }
        });
}

function SuggestImpuesto() {
    new Ajax.Request(WEB_ROOT + '/ajax/suggest_x.php',
        {
            parameters: {value: $('impuestoId').value, type: "impuesto"},
            method: 'post',
            onSuccess: function (transport) {
                var response = transport.responseText || "no response text";
                $('suggestionImpuestoDiv').show();
                $('suggestionImpuestoDiv').innerHTML = response;
                var elements = $$('span.resultSuggestImpuesto');
                AddSuggestImpuestoListener(elements);
            },
            onFailure: function () {
                alert('Something went wrong...')
            }
        });
}

function HideSuggestions() {
    $('suggestionDiv').hide();
}

function FillImpuestoData() {
    $('loadingDivImpuesto').innerHTML = '<img src="' + WEB_ROOT + '/images/load.gif" />';

//	$('suggestionProductDiv').hide();
    new Ajax.Request(WEB_ROOT + '/ajax/fill_form.php',
        {
            parameters: {value: $('impuestoId').value, type: "impuesto"},
            method: 'post',
            onSuccess: function (transport) {
                var response = transport.responseText || "no response text";
                var splitResponse = response.split("{#}");
                $('impuestoId').value = splitResponse[0];
                $('tasa').value = splitResponse[1];
                $('tipo').value = splitResponse[2];
                $('iva').value = splitResponse[3];
                $('loadingDivImpuesto').innerHTML = '';
            },
            onFailure: function () {
                alert('Something went wrong...')
            }
        });

}

function FillConceptoData() {
    $('loadingDivConcepto').innerHTML = '<img src="' + WEB_ROOT + '/images/load.gif" />';
    new Ajax.Request(WEB_ROOT + '/ajax/fill_form.php',
        {
            parameters: {value: $('noIdentificacion').value, type: "producto"},
            method: 'post',
            onSuccess: function (transport) {
                var response = transport.responseText || "no response text";
                var splitResponse = response.split("{#}");
                $('descripcion').value = splitResponse[0];
                $('valorUnitario').value = splitResponse[1];
                $('nombreServicioOculto').value = splitResponse[0];
                $('unidad').value = splitResponse[2];
                $('loadingDivConcepto').innerHTML = '';
                jQ('form#conceptoForm .divVincularToServicio').show()
                UpdateValorUnitarioConIva();
            },
            onFailure: function () {
                alert('Something went wrong...')
            }
        });

}

function UpdateValorUnitarioConIva() {
    var ish = parseInt($('ishConcepto').value) || 0;
    ish = ish / 100;

    valor = parseFloat($('valorUnitario').value) || 0;

    valorConIva = valor + (valor * (parseInt($('tasaIva').value) / 100));
    valorConIsh = valorConIva + (valor * ish);

    $('valorUnitarioCI').value = valorConIsh.toFixed(6);
}

function UpdateValorUnitarioSinIva(valor) {
    var ish = parseInt($('ishConcepto').value) || 0;
    ish = ish / 100;

    valor = parseFloat($('valorUnitarioCI').value) || 0;
    valorSinIva = parseFloat(valor) || 0;
    tasaIva = 1 + (parseInt($('tasaIva').value) / 100);
    tasaTotal = tasaIva + ish;

    valorSinIva = valorSinIva / tasaTotal;
    $('valorUnitario').value = valorSinIva.toFixed(6);
}

function FillDatosFacturacion(id) {
    $('loadingDivDatosFactura').innerHTML = '<img src="' + WEB_ROOT + '/images/load.gif" />';
    new Ajax.Request(WEB_ROOT + '/ajax/fill_form.php',
        {
            parameters: {value: id, type: "datosFacturacion"},
            method: 'post',
            onSuccess: function (transport) {
                var response = transport.responseText || "no response text";
                var splitResponse = response.split("{#}");
                $('razonSocial').value = splitResponse[3];
                $('rfc').value = splitResponse[15];
                $('userId').value = splitResponse[16];
                $('calle').value = splitResponse[6];
                $('pais').value = splitResponse[7];
                $('loadingDivDatosFactura').innerHTML = '';

                var useServiceConcept = confirm(' ¿ Desea cargar los sevicios facturables de esta empresa como conceptos?');
                if (useServiceConcept)
                    loadConceptosFromServices();
                CancelarConcepto();

            },
            onFailure: function () {
                alert('Something went wrong...')
            }
        });

}

function loadConceptosFromServices() {
    $('conceptos').innerHTML = '<div align="center"><img src="' + WEB_ROOT + '/images/load.gif" /></div>';
    new Ajax.Request(WEB_ROOT + '/ajax/cfdi33.php', {
        method: 'post',
        parameters: {value: $('userId').value, type: "loadConceptoFromService"},
        onSuccess: function (transport) {
            var response = transport.responseText || "no response text";
            var splitResponse = response.split("|");
            if (splitResponse[0] == "fail") {
                $('divStatus').innerHTML = splitResponse[1];
                $('centeredDiv').show();
                grayOut(true);
            }
            $('conceptos').innerHTML = splitResponse[2];
            var elements = $$('span.linkBorrar');
            AddBorrarConceptoListeners(elements);
            UpdateTotalesDesglosados();
        },
        onFailure: function () {
            alert('Something went wrong...')
        }
    });
}

function UpdateIepsConcepto() {
    $('iepsConcepto').value = $('porcentajeIEPS').value;
}

function AgregarConcepto() {
    /*var descripcion = $("descripcion").value;
    descripcion = descripcion.replace("+", "[%]MAS[%]");
    var idContractToFactura =  jQ('form#nuevaFactura #userId').val()
    $("descripcion").value = descripcion;

    new Ajax.Request(WEB_ROOT + '/ajax/cfdi33.php',
        {
            method: 'post',
            parameters: $('conceptoForm').serialize(true) + '&userId=' + idContractToFactura,
            onSuccess: function (transport) {
                var response = transport.responseText || "no response text";
                var splitResponse = response.split("|");

                if (splitResponse[0] == "fail") {
                    $('divStatus').innerHTML = splitResponse[1];
                    $('centeredDiv').show();
                    grayOut(true);
                }
                $('conceptos').innerHTML = splitResponse[2];
                var elements = $$('span.linkBorrar');
                $('conceptoForm').reset()
                $('agregarConceptoDivSpan').innerHTML = 'Agregar'
                jQ('form#conceptoForm #conceptoId').val('')
                jQ('form#conceptoForm .divVincularToServicio').show()
                AddBorrarConceptoListeners(elements);
                UpdateTotalesDesglosados();
            },
            onFailure: function () {
                alert('Something went wrong...')
            }
        });
*/
    var descripcion = jQ("form#conceptoForm #descripcion").val();
    descripcion = descripcion.replace("+", "[%]MAS[%]");
    var idContractToFactura =  jQ('form#nuevaFactura #userId').val()
    jQ("#descripcion").val(descripcion);
    var formData = new FormData(document.getElementById("conceptoForm"));
    formData.append('userId', idContractToFactura)
    if(jQ('#vincularToServicio').is(':checked') && idContractToFactura === '') {
        alert('Elija una razon social, si pretende vincular el concepto.')
        return
    }

    jQ.ajax({
        url: WEB_ROOT + '/ajax/cfdi33.php',
        type: 'post',
        contentType: false,
        processData: false,
        data: formData,
        beforeSend: function () {
            jQ('#conceptos').html('<div align="center"><img src="' + WEB_ROOT + '/images/load.gif" /></div>')
        },
        success: function (response) {
            var splitResponse = response.split("|");
            if (splitResponse[0] === "fail") {
                jQ('#divStatus').html(splitResponse[1])
                jQ('#centeredDiv').show();
                grayOut(true);
            }
            jQ('#conceptos').html(splitResponse[2])
            var elements = $$('span.linkBorrar');
            $('conceptoForm').reset()
            jQ('#agregarConceptoDivSpan').html('Agregar');
            jQ('form#conceptoForm #conceptoId').val('')
            jQ('form#conceptoForm .divVincularToServicio').show()
            AddBorrarConceptoListeners(elements);
            UpdateTotalesDesglosados();
        },
        error: function () {
            alert('Something went wrong...')
        },
    })
}

function CancelarConcepto () {
    $('conceptoForm').reset()
    jQ('form#conceptoForm #conceptoId').val('')
    jQ('form#conceptoForm .divVincularToServicio').val('')
    jQ('form#conceptoForm  #nombreServicioOculto').val('')
}

function AgregarImpuesto() {
    $('impuestos').innerHTML = '<div align="center"><img src="' + WEB_ROOT + '/images/load.gif" /></div>';
    var form = $('impuestoForm').serialize();
    new Ajax.Request(WEB_ROOT + '/ajax/sistema.php',
        {
            parameters: {form: form, type: "agregarImpuesto"},
            method: 'post',
            onSuccess: function (transport) {
                var response = transport.responseText || "no response text";
                var splitResponse = response.split("|");

                if (splitResponse[0] == "fail") {
                    $('divStatus').innerHTML = splitResponse[1];
                    $('centeredDiv').show();
                    grayOut(true);
                }
                $('impuestos').innerHTML = splitResponse[2];
                var elements = $$('span.linkBorrarImpuesto');
                AddBorrarImpuestosListeners(elements);
                UpdateTotalesDesglosados();
            },
            onFailure: function () {
                alert('Something went wrong...')
            }
        });
}

function BorrarConcepto(e, id) {
    new Ajax.Request(WEB_ROOT + '/ajax/sistema.php',
        {
            parameters: {id: id, type: "borrarConcepto"},
            method: 'post',
            onSuccess: function (transport) {
                var response = transport.responseText || "no response text";
                $('conceptos').innerHTML = response;
                var elements = $$('span.linkBorrar');
                AddBorrarConceptoListeners(elements)
                UpdateTotalesDesglosados();

            },
            onFailure: function () {
                alert('Something went wrong...')
            }
        });
}

function CargarConcepto(index) {
    jQ.ajax(
        {
            url:WEB_ROOT+'/ajax/cfdi33.php',
            type:'post',
            dataType:'json',
            data:{type:'getConcepto', index},
            success:function (response) {
                jQ('form#conceptoForm  #conceptoId').val(index)
                jQ('form#conceptoForm  #cantidad').val(response.cantidad)
                jQ('form#conceptoForm  #noIdentificacion').val(response.noIdentificacion)
                jQ('form#conceptoForm  #unidad').val(response.unidad)
                jQ('form#conceptoForm  #valorUnitario').val(response.valorUnitario)
                var pUci = parseFloat(response.valorUnitario)+parseFloat(response.totalIva)
                jQ('form#conceptoForm  #valorUnitarioCI').val(pUci)
                jQ('form#conceptoForm  #excentoIva').val(response.excentoIva)
                jQ('form#conceptoForm  #c_ClaveProdServ').val(response.claveProdServ)
                jQ('form#conceptoForm  #c_ClaveUnidad').val(response.claveUnidad)
                jQ('form#conceptoForm  #descripcion').val(response.descripcion)
                jQ('form#conceptoForm  #fechaCorrespondiente').val(response.fechaCorrespondiente)
                jQ('form#conceptoForm  #nombreServicioOculto').val(response.nombreServicioOculto)
                parseInt(response.servicioId) > 0
                    ? jQ('form#conceptoForm  .divVincularToServicio').hide()
                    : jQ('form#conceptoForm  .divVincularToServicio').show()
                jQ('#agregarConceptoDivSpan').html('Actualizar')
            }
        }
    );
}

function BorrarImpuesto(e, id) {
    id = id.strip();
    new Ajax.Request(WEB_ROOT + '/ajax/sistema.php',
        {
            parameters: {id: id, type: "borrarImpuesto"},
            method: 'post',
            onSuccess: function (transport) {
                var response = transport.responseText || "no response text";
                $('impuestos').innerHTML = response;
                var elements = $$('span.linkBorrarImpuesto');
                AddBorrarImpuestosListeners(elements)
                UpdateTotalesDesglosados();

            },
            onFailure: function () {
                alert('Something went wrong...')
            }
        });
}

function AddBorrarConceptoListeners(elements) {
    elements.each(
        function (e) {
            var id = $(e).up(0).previous(7).innerHTML;
            Event.observe(e, "click", function (e) {
                BorrarConcepto(e, id);
            });
        }
    );
}

function AddBorrarImpuestosListeners(elements) {
    elements.each(
        function (e) {
            var id = $(e).up(0).previous(4).innerHTML;
            Event.observe(e, "click", function (e) {
                BorrarImpuesto(e, id);
            });
        }
    );
}

function UpdateTotalesDesglosados() {
    var form = $('nuevaFactura').serialize();
    new Ajax.Request(WEB_ROOT + '/ajax/cfdi33.php',
        {
            parameters: {form: form, type: "updateTotalesDesglosados"},
            method: 'post',
            onSuccess: function (transport) {
                var response = transport.responseText || "no response text";
                $('totalesDesglosadosDiv').innerHTML = response;
            },
            onFailure: function () {
                alert('Something went wrong...')
            }
        });
}

function GenerarComprobante(format) {
    var modo_factura = $('modo_factura').value
    var message = parseInt(modo_factura) === 2
        ? "Estas a punto de generar una factura que sustituira el folio: " + $('serieAnterior').value+$('folioAnterior').value
        + ', asegurate que la información este correcta.'
        : "Realmente deseas generar un comprobante. asegurate de que lo estes generando sean con los datos correctos.";
    if (!confirm(message)) {
        return;
    }

    $('nuevaFactura').enable();
    var nuevaFactura = $('nuevaFactura').serialize();
    $('nuevaFactura').disable();
    $('rfc').enable();
    $('userId').enable();
    $('formaDePago').enable();
    $('condicionesDePago').enable();
    $('metodoDePago').enable();
    $('tasaIva').enable();
    $('tiposDeMoneda').enable();
    $('porcentajeRetIva').enable();
    $('porcentajeDescuento').enable();
    $('tipoDeCambio').enable();
    $('porcentajeRetIsr').enable();
    $('tiposComprobanteId').enable();
    $('sucursalId').enable();
    $('porcentajeIEPS').enable();
    $('nuevaFactura').enable();
    $('usoCfdi').enable();

    if ($('reviso')) var reviso = $('reviso').value;
    else var reviso = "";

    if ($('autorizo')) var autorizo = $('autorizo').value;
    else var autorizo = "";

    if ($('recibio')) var recibio = $('recibio').value;
    else var recibio = "";

    if ($('vobo')) var vobo = $('vobo').value;
    else var vobo = "";

    if ($('pago')) var pago = $('pago').value;
    else var pago = "";

    if ($('tiempoLimite')) var tiempoLimite = $('tiempoLimite').value;
    else var tiempoLimite = "";

    if ($('fechaSobreDia')) var fechaSobreDia = $('fechaSobreDia').value;
    else var fechaSobreDia = "";

    if ($('fechaSobreMes')) var fechaSobreMes = $('fechaSobreMes').value;
    else var fechaSobreMes = "";

    if ($('fechaSobreAnio')) var fechaSobreAnio = $('fechaSobreAnio').value;
    else var fechaSobreAnio = "";

    if ($('folioSobre')) var folioSobre = $('folioSobre').value;
    else var folioSobre = "";

    var cuentaPorPagar = "";

    if ($('formatoNormal')) {
        if ($('formatoNormal').checked)
            var formatoNormal = $('formatoNormal').value;
        else
            var formatoNormal = 0;
    } else {
        var formatoNormal = 0;
    }

    if ($('banco')) var banco = $('banco').value;
    else var banco = 0;

    if ($('fechaDeposito')) var fechaDeposito = $('fechaDeposito').value;
    else var fechaDeposito = 0;

    if ($('referencia')) var referencia = $('referencia').value;
    else var referencia = 0;

    new Ajax.Request(WEB_ROOT + '/ajax/cfdi33.php',
        {
            parameters: {
                nuevaFactura: nuevaFactura,
                observaciones: $('observaciones').value,
                type: 'generarComprobante',
                reviso: reviso,
                autorizo: autorizo,
                recibio: recibio,
                vobo: vobo,
                pago: pago,
                fechaSobreDia: fechaSobreDia,
                fechaSobreMes: fechaSobreMes,
                fechaSobreAnio: fechaSobreAnio,
                folioSobre: folioSobre,
                tiempoLimite: tiempoLimite,
                cuentaPorPagar: cuentaPorPagar,
                formatoNormal: formatoNormal,
                format: format,
                banco: banco,
                fechaDeposito: fechaDeposito,
                referencia: referencia,
            },
            method: 'post',
            onLoading: function () {
                $('showFactura').innerHTML = '<div align="center"><img src="' + WEB_ROOT + '/images/load.gif" /><br>Generando Comprobante, este proceso puede tardar unos segundos</div>';
                $('reemplazarBoton').style.display = 'none';
            },
            onSuccess: function (transport) {
                var response = transport.responseText || "no response text";
                var splitResponse = response.split("|");
                $('showFactura').innerHTML = "";
                $('reemplazarBoton').style.display = 'block';
                if (splitResponse[0] == "ok") {
                    $('showFactura').innerHTML = splitResponse[1];
                    $('reemplazarBoton').style.display = 'block';
                    if (splitResponse[2] === 'real')
                        $('reemplazarBoton').innerHTML = '<a class="button" href="javascript:;"  onclick="location.reload()" title="Generar nuevo comprobante"><span id="anonymous_element_1">Nuevo comprobante</span></a>';
                } else {
                    $('divStatus').innerHTML = splitResponse[1];
                    $('centeredDiv').show();
                    grayOut(true);
                }
            },
            onFailure: function () {
                alert('Something went wrong...')
            }
        });
}

function ShowPopUpDiv(id) {
    grayOut(true);
    $('fview').show();
    if (id == 0) {
        $('fview').hide();
        grayOut(false);
        return;
    }

    new Ajax.Request(WEB_ROOT + '/ajax/popupdivtest.php',
        {
            method: 'post',
            onSuccess: function (transport) {
                var response = transport.responseText || "no response text";
                $('fview').innerHTML = response;
                Event.observe($('closePopUpDiv'), "click", function () {
                    ShowPopUpDiv(0);
                });
                new Draggable('fview', {scroll: window, handle: 'popupheader'});

            },
            onFailure: function () {
                alert('Something went wrong...')
            }
        });
}

Event.observe(window, 'load', function () {
    if ($('rfc')) {
        Event.observe($('rfc'), "keyup", function () {
            SuggestUser();
        });
    }
    if ($('rfc')) {
        Event.observe($('noIdentificacion'), "keyup", function () {
            SuggestProduct();
            //FillConceptoData();
        });
    }
    if ($('rfc')) {
        Event.observe($('agregarConceptoDiv'), "click", AgregarConcepto);
    }
    if ($('rfc')) {
        Event.observe($('generarFactura'), "click", function () {
            GenerarComprobante('generar');
        });
    }
    if ($('rfc')) {
        if ($('agregarImpuestoDiv')) {
            Event.observe($('agregarImpuestoDiv'), "click", AgregarImpuesto);
        }
        Event.observe($('vistaPrevia'), "click", function () {
            GenerarComprobante('vistaPrevia');
        });

    }

    if ($('impuestoId')) {
        Event.observe($('impuestoId'), "keyup", function () {
            SuggestImpuesto();
            FillImpuestoData();
        });
    }

    if ($$('span.linkBorrar')) {
        var elements = $$('span.linkBorrar');
    }

    AddSuggestListenerInvoice = function (e) {
        var el = e.element();
        var del = el.hasClassName('suggestUserDiv');
        var id = el.identify();
        if (del == true) {
            FillRFCInvoice(1, id);
            return;
        }

        del = el.hasClassName('suggestProductoDiv');
        if (del == true) {
            FillNoIdentificacion(1, id);
            return;
        }

        del = el.hasClassName('suggestImpuestoDiv');
        if (del == true) {
            FillImpuestoId(1, id);
            return;
        }

        del = el.hasClassName('closeSuggestUserDiv');
        if (del == true) {
            $('suggestionDiv').hide();
            return;
        }

        del = el.hasClassName('closeSuggestProductoDiv');
        if (del == true) {
            $('suggestionProductDiv').hide();
            return;
        }

        del = el.hasClassName('closeSuggestImpuestoDiv');
        if (del == true) {
            $('suggestionImpuestoDiv').hide();
            return;
        }


    }

    if ($('divForm') != undefined) {
        $('divForm').observe("click", AddSuggestListenerInvoice);
    }
});

function regresarVentas() {
    alert("hola");
    window.location = WEB_ROOT + "/reporte-ventas";


}

function EnviarEmail(id) {

    new Ajax.Request(WEB_ROOT + '/ajax/manage-facturas.php',
        {
            method: 'post',
            parameters: {type: 'enviar_email', id_comprobante: id},
            onSuccess: function (transport) {
                var response = transport.responseText || "no response text";
                var splitResponse = response.split("[#]");
                if (splitResponse[0] == "ok") {
                    ShowStatusPopUp(splitResponse[1])
                }

            },
            onFailure: function () {
                alert('Something went wrong...')
            }
        });

}//EnviarEmail

jQ(function() {
    jQ(document).on('change', '#modo_factura', function() {
        if(parseInt(this.value) === 1) {
            jQ('.normalInvoice').removeClass('noShow')
            jQ('.sustitucionInvoice').addClass('noShow')
            jQ('#parent').val('')
        } else if(parseInt(this.value) === 2) {
            jQ('.normalInvoice').addClass('noShow')
            jQ('.sustitucionInvoice').removeClass('noShow')
            jQ('#parent').val('')
        } else {
            jQ('.normalInvoice').addClass('noShow')
            jQ('.sustitucionInvoice').addClass('noShow')
            jQ('#parent').val('')
        }
        // por cada cambio en el seleccionable limpiar conceptos.
        jQ.ajax({
                url:WEB_ROOT+'/ajax/cfdi33.php',
                type:'post',
                data:{type:'clenAllConcepto'},
                success:function () {
                    jQ('#conceptos').html('Ninguno (Has click en Agregar para agregar un concepto)')
                    CancelarConcepto()
                    UpdateTotalesDesglosados();
                }
            }
        )
    })
    jQ(document).on('click', '#btnLoadDataBefore', function () {
        var $serie =  jQ('#serieAnterior').val();
        var $folio =  jQ('#folioAnterior').val();
        jQ.ajax(
            {
                url:WEB_ROOT+'/ajax/cfdi33.php',
                type:'post',
                data:{type:'loadConceptoAndDataCompany', serie:$serie, folio:$folio },
                beforeSend: function () {
                    jQ('#loading-cargar-dato').show()
                    jQ('#btnLoadDataBefore').hide()
                    $('conceptoForm').reset()
                },
                success:function (response) {
                    jQ('#loading-cargar-dato').hide()
                    jQ('#btnLoadDataBefore').show()
                    var splitResponse = response.split('[#]');
                    if(splitResponse[0] == 'fail') {
                        ShowStatusPopUp(splitResponse[1])
                        return
                    }

                    jQ('.normalInvoice').removeClass('noShow');
                    jQ('.sustitucionInvoice').addClass('noShow')

                    jQ('#parent').val(splitResponse[2]);
                    jQ('#razonSocial').val(splitResponse[3]);
                    jQ('#rfc').val(splitResponse[4]);
                    jQ('#userId').val(splitResponse[5]);
                    jQ('#calle').val(splitResponse[6]);
                    jQ('#pais').val(splitResponse[7]);
                    jQ('#tiposComprobanteId').val(splitResponse[8]);
                    jQ('#formaDePago').val(splitResponse[9]);
                    jQ('#metodoDePago').val(splitResponse[10]);

                    jQ('#conceptos').html(splitResponse[1]);
                    var elements = $$('span.linkBorrar');
                    AddBorrarConceptoListeners(elements);
                    UpdateTotalesDesglosados();
                }
            }
        )
    })
})
