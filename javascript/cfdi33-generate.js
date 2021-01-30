var DOC_ROOT = "../";
var DOC_ROOT_TRUE = "../";
var DOC_ROOT_SECTION = "../../";

function SuggestUser()
{
    new Ajax.Request(WEB_ROOT+'/ajax/suggest.php',
        {
            parameters: {value: $('rfc').value,activos:true},
            method:'post',
            onSuccess: function(transport){
                var response = transport.responseText || "no response text";
                $('suggestionDiv').show();
                $('suggestionDiv').innerHTML = response;
                AddSuggestListenerInvoice();
            },
            onFailure: function(){ alert('Something went wrong...') }
        });
}

function FillRFCInvoice(elem, id)
{
    $('suggestionDiv').hide();
    FillDatosFacturacion(id);
}

function FillNoIdentificacion(elem, id)
{
    $('noIdentificacion').value = id;
    $('suggestionProductDiv').hide();
    FillConceptoData();
}

function FillImpuestoId(elem, id)
{
    $('impuestoId').value = id;
    $('suggestionImpuestoDiv').hide();
    FillImpuestoData();
}

function SuggestProduct()
{
    new Ajax.Request(WEB_ROOT+'/ajax/suggest_x.php',
        {
            parameters: {value: $('noIdentificacion').value, type: "producto"},
            method:'post',
            onSuccess: function(transport){
                var response = transport.responseText || "no response text";
                $('suggestionProductDiv').show();
                $('suggestionProductDiv').innerHTML = response;
            },
            onFailure: function(){ alert('Something went wrong...') }
        });
}

function SuggestImpuesto()
{
    new Ajax.Request(WEB_ROOT+'/ajax/suggest_x.php',
        {
            parameters: {value: $('impuestoId').value, type: "impuesto"},
            method:'post',
            onSuccess: function(transport){
                var response = transport.responseText || "no response text";
                $('suggestionImpuestoDiv').show();
                $('suggestionImpuestoDiv').innerHTML = response;
                var elements = $$('span.resultSuggestImpuesto');
                AddSuggestImpuestoListener(elements);
            },
            onFailure: function(){ alert('Something went wrong...') }
        });
}

function HideSuggestions()
{
    $('suggestionDiv').hide();
}

function FillImpuestoData()
{
    $('loadingDivImpuesto').innerHTML = '<img src="'+WEB_ROOT+'/images/load.gif" />';

//	$('suggestionProductDiv').hide();
    new Ajax.Request(WEB_ROOT+'/ajax/fill_form.php',
        {
            parameters: {value: $('impuestoId').value, type: "impuesto"},
            method:'post',
            onSuccess: function(transport){
                var response = transport.responseText || "no response text";
                var splitResponse = response.split("{#}");
                $('impuestoId').value = splitResponse[0];
                $('tasa').value = splitResponse[1];
                $('tipo').value = splitResponse[2];
                $('iva').value = splitResponse[3];
                $('loadingDivImpuesto').innerHTML = '';
            },
            onFailure: function(){ alert('Something went wrong...') }
        });

}

function FillConceptoData()
{
    $('loadingDivConcepto').innerHTML = '<img src="'+WEB_ROOT+'/images/load.gif" />';
    new Ajax.Request(WEB_ROOT+'/ajax/fill_form.php',
        {
            parameters: {value: $('noIdentificacion').value, type: "producto"},
            method:'post',
            onSuccess: function(transport){
                var response = transport.responseText || "no response text";
                var splitResponse = response.split("{#}");
                $('descripcion').value = splitResponse[0];
                $('valorUnitario').value = splitResponse[1];
                $('unidad').value = splitResponse[2];
                $('loadingDivConcepto').innerHTML = '';
                UpdateValorUnitarioConIva();
            },
            onFailure: function(){ alert('Something went wrong...') }
        });

}

function UpdateValorUnitarioConIva()
{
    var ish = parseInt($('ishConcepto').value) || 0;
    ish = ish / 100;

    valor = parseFloat($('valorUnitario').value) || 0;

    valorConIva = valor + (valor * (parseInt($('tasaIva').value) / 100));
    valorConIsh = valorConIva + (valor * ish);

    $('valorUnitarioCI').value = valorConIsh.toFixed(6);
}

function UpdateValorUnitarioSinIva(valor)
{
    var ish = parseInt($('ishConcepto').value) || 0;
    ish = ish / 100;

    valor = parseFloat($('valorUnitarioCI').value) || 0;
    valorSinIva = parseFloat(valor) || 0;
    tasaIva = 1 + (parseInt($('tasaIva').value) / 100);
    tasaTotal = tasaIva + ish;

    valorSinIva = valorSinIva / tasaTotal;
    $('valorUnitario').value = valorSinIva.toFixed(6);
}

function FillDatosFacturacion(id)
{
    $('loadingDivDatosFactura').innerHTML = '<img src="'+WEB_ROOT+'/images/load.gif" />';
    new Ajax.Request(WEB_ROOT+'/ajax/fill_form.php',
        {
            parameters: {value: id, type: "datosFacturacion"},
            method:'post',
            onSuccess: function(transport){
                var response = transport.responseText || "no response text";
                var splitResponse = response.split("{#}");
                $('razonSocial').value = splitResponse[3];
                $('rfc').value = splitResponse[15];
                $('userId').value = splitResponse[16];
                $('calle').value = splitResponse[6];
                $('pais').value = splitResponse[7];
                $('loadingDivDatosFactura').innerHTML = '';

                var useServiceConcept =  confirm(' ¿ Desea cargar los sevicios facturables de esta empresa como conceptos?');
                if(useServiceConcept)
                    loadConceptosFromServices();

            },
            onFailure: function(){ alert('Something went wrong...') }
        });

}
function loadConceptosFromServices () {
    $('conceptos').innerHTML = '<div align="center"><img src="'+WEB_ROOT+'/images/load.gif" /></div>';
    new Ajax.Request(WEB_ROOT+'/ajax/cfdi33.php', {
            method:'post',
            parameters: { value: $('userId').value, type: "loadConceptoFromService" },
            onSuccess: function(transport) {
                var response = transport.responseText || "no response text";
                var splitResponse = response.split("|");
                if(splitResponse[0] == "fail")
                {
                    $('divStatus').innerHTML = splitResponse[1];
                    $('centeredDiv').show();
                    grayOut(true);
                }
                $('conceptos').innerHTML = splitResponse[2];
                var elements = $$('span.linkBorrar');
                AddBorrarConceptoListeners(elements);
                UpdateTotalesDesglosados();
            },
            onFailure: function(){ alert('Something went wrong...') }
        });
}
function UpdateIepsConcepto()
{
    $('iepsConcepto').value = $('porcentajeIEPS').value;
}

function AgregarConcepto()
{
    var descripcion = $("descripcion").value;
    descripcion = descripcion.replace("+","[%]MAS[%]");
    $("descripcion").value = descripcion;
    $('conceptos').innerHTML = '<div align="center"><img src="'+WEB_ROOT+'/images/load.gif" /></div>';
    new Ajax.Request(WEB_ROOT+'/ajax/cfdi33.php',
        {
            method:'post',
            parameters: $('conceptoForm').serialize(true),
            onSuccess: function(transport){
                var response = transport.responseText || "no response text";
                var splitResponse = response.split("|");

                if(splitResponse[0] == "fail")
                {
                    $('divStatus').innerHTML = splitResponse[1];
                    $('centeredDiv').show();
                    grayOut(true);
                }
                $('conceptos').innerHTML = splitResponse[2];
                var elements = $$('span.linkBorrar');
                AddBorrarConceptoListeners(elements);
                UpdateTotalesDesglosados();
            },
            onFailure: function(){ alert('Something went wrong...') }
        });
}

function AgregarImpuesto()
{
    $('impuestos').innerHTML = '<div align="center"><img src="'+WEB_ROOT+'/images/load.gif" /></div>';
    var form = $('impuestoForm').serialize();
    new Ajax.Request(WEB_ROOT+'/ajax/sistema.php',
        {
            parameters: {form: form, type: "agregarImpuesto"},
            method:'post',
            onSuccess: function(transport){
                var response = transport.responseText || "no response text";
                var splitResponse = response.split("|");

                if(splitResponse[0] == "fail")
                {
                    $('divStatus').innerHTML = splitResponse[1];
                    $('centeredDiv').show();
                    grayOut(true);
                }
                $('impuestos').innerHTML = splitResponse[2];
                var elements = $$('span.linkBorrarImpuesto');
                AddBorrarImpuestosListeners(elements);

                UpdateTotalesDesglosados();
            },
            onFailure: function(){ alert('Something went wrong...') }
        });
}


function BorrarConcepto(e, id)
{
    new Ajax.Request(WEB_ROOT+'/ajax/sistema.php',
        {
            parameters: {id: id, type: "borrarConcepto"},
            method:'post',
            onSuccess: function(transport){
                var response = transport.responseText || "no response text";
                $('conceptos').innerHTML = response;
                var elements = $$('span.linkBorrar');
                AddBorrarConceptoListeners(elements)
                UpdateTotalesDesglosados();

            },
            onFailure: function(){ alert('Something went wrong...') }
        });
}

function BorrarImpuesto(e, id)
{
    id = id.strip();
    new Ajax.Request(WEB_ROOT+'/ajax/sistema.php',
        {
            parameters: {id: id, type: "borrarImpuesto"},
            method:'post',
            onSuccess: function(transport){
                var response = transport.responseText || "no response text";
                $('impuestos').innerHTML = response;
                var elements = $$('span.linkBorrarImpuesto');
                AddBorrarImpuestosListeners(elements)
                UpdateTotalesDesglosados();

            },
            onFailure: function(){ alert('Something went wrong...') }
        });
}

function AddBorrarConceptoListeners(elements)
{
    elements.each(
        function(e) {
            var id = $(e).up(0).previous(8).innerHTML;
            console.log(id);
            Event.observe(e, "click", function (e) {
                BorrarConcepto(e, id);
            });
        }
    );
}

function AddBorrarImpuestosListeners(elements)
{
    elements.each(
        function(e) {
            var id = $(e).up(0).previous(4).innerHTML;
            Event.observe(e, "click", function (e) {
                BorrarImpuesto(e, id);
            });
        }
    );
}

function UpdateTotalesDesglosados()
{
    var form = $('nuevaFactura').serialize();
    new Ajax.Request(WEB_ROOT+'/ajax/cfdi33.php',
        {
            parameters: {form: form, type: "updateTotalesDesglosados"},
            method:'post',
            onSuccess: function(transport){
                var response = transport.responseText || "no response text";
                $('totalesDesglosadosDiv').innerHTML = response;
            },
            onFailure: function(){ alert('Something went wrong...') }
        });
}

function GenerarComprobante(format)
{
    var message = "Realmente deseas generar un comprobante. Asegurate de que lo estes generando para tu RFC Correcto.";
    if(!confirm(message))
    {
        return;
    }

    $('showFactura').innerHTML = '<div align="center"><img src="'+WEB_ROOT+'/images/load.gif" /><br>Generando Comprobante, este proceso puede tardar unos segundos</div>';

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

    if($('reviso')) var reviso = $('reviso').value;
    else var reviso = "";

    if($('autorizo')) var autorizo = $('autorizo').value;
    else var autorizo = "";

    if($('recibio')) var recibio = $('recibio').value;
    else var recibio = "";

    if($('vobo')) var vobo = $('vobo').value;
    else var vobo = "";

    if($('pago')) var pago = $('pago').value;
    else var pago = "";

    if($('tiempoLimite')) var tiempoLimite = $('tiempoLimite').value;
    else var tiempoLimite = "";

    if($('fechaSobreDia')) var fechaSobreDia = $('fechaSobreDia').value;
    else var fechaSobreDia = "";

    if($('fechaSobreMes')) var fechaSobreMes = $('fechaSobreMes').value;
    else var fechaSobreMes = "";

    if($('fechaSobreAnio')) var fechaSobreAnio = $('fechaSobreAnio').value;
    else var fechaSobreAnio = "";

    if($('folioSobre')) var folioSobre = $('folioSobre').value;
    else var folioSobre = "";

    //if($('cuentaPorPagar').checked) var cuentaPorPagar = $('cuentaPorPagar').value;
    //else var cuentaPorPagar = "";
    var cuentaPorPagar = "";

    if($('formatoNormal')){
        if($('formatoNormal').checked)
            var formatoNormal = $('formatoNormal').value;
        else
            var formatoNormal = 0;
    }else{
        var formatoNormal = 0;
    }

    if($('banco')) var banco = $('banco').value;
    else var banco = 0;

    if($('fechaDeposito')) var fechaDeposito = $('fechaDeposito').value;
    else var fechaDeposito = 0;

    if($('referencia')) var referencia = $('referencia').value;
    else var referencia = 0;

    new Ajax.Request(WEB_ROOT+'/ajax/cfdi33.php',
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
                tiempoLimite:tiempoLimite,
                cuentaPorPagar:cuentaPorPagar,
                formatoNormal:formatoNormal,
                format:format,
                banco:banco,
                fechaDeposito:fechaDeposito,
                referencia:referencia,
            },
            method:'post',
            onSuccess: function(transport){
                var response = transport.responseText || "no response text";
                console.log(response);
                var splitResponse = response.split("|");

                $('showFactura').innerHTML = "";

                if(splitResponse[0] == "ok"){
                    $('showFactura').innerHTML = splitResponse[1];
                    //$("reemplazarBoton").hide();
                }else{
                    $('divStatus').innerHTML = splitResponse[1];
                    $('centeredDiv').show();
                    grayOut(true);
                }
            },
            onFailure: function(){ alert('Something went wrong...') }
        });
}

function ShowPopUpDiv(id)
{
    grayOut(true);
    $('fview').show();
    if(id == 0)
    {
        $('fview').hide();
        grayOut(false);
        return;
    }

    new Ajax.Request(WEB_ROOT+'/ajax/popupdivtest.php',
        {
            method:'post',
            onSuccess: function(transport){
                var response = transport.responseText || "no response text";
                $('fview').innerHTML = response;
                Event.observe($('closePopUpDiv'), "click", function(){ ShowPopUpDiv(0); });
                new Draggable('fview',{scroll:window,handle:'popupheader'});

            },
            onFailure: function(){ alert('Something went wrong...') }
        });
}

Event.observe(window, 'load', function() {
    if($('rfc'))
    {
        Event.observe($('rfc'), "keyup", function(){ SuggestUser(); });
    }
    if($('rfc'))
    {
        Event.observe($('noIdentificacion'), "keyup", function(){ SuggestProduct(); FillConceptoData();});
    }
    if($('rfc'))
    {
        Event.observe($('agregarConceptoDiv'), "click", AgregarConcepto);
    }
    if($('rfc'))
    {
        Event.observe($('generarFactura'), "click", function() {
            GenerarComprobante('generar');
        });
    }
    if($('rfc'))
    {
        if($('agregarImpuestoDiv'))
        {
            Event.observe($('agregarImpuestoDiv'), "click", AgregarImpuesto);
        }
        Event.observe($('vistaPrevia'), "click", function() {
            GenerarComprobante('vistaPrevia');
        });

    }

    if($('impuestoId'))
    {
        Event.observe($('impuestoId'), "keyup", function(){ SuggestImpuesto(); FillImpuestoData();});
    }

    if($$('span.linkBorrar'))
    {
        var elements = $$('span.linkBorrar');
    }

    AddSuggestListenerInvoice = function(e) {
        var el = e.element();
        var del = el.hasClassName('suggestUserDiv');
        var id = el.identify();
        if(del == true) {
            FillRFCInvoice(1, id);
            return;
        }

        del = el.hasClassName('suggestProductoDiv');
        if(del == true){
            FillNoIdentificacion(1, id);
            return;
        }

        del = el.hasClassName('suggestImpuestoDiv');
        if(del == true){
            FillImpuestoId(1, id);
            return;
        }

        del = el.hasClassName('closeSuggestUserDiv');
        if(del == true){
            $('suggestionDiv').hide();
            return;
        }

        del = el.hasClassName('closeSuggestProductoDiv');
        if(del == true){
            $('suggestionProductDiv').hide();
            return;
        }

        del = el.hasClassName('closeSuggestImpuestoDiv');
        if(del == true){
            $('suggestionImpuestoDiv').hide();
            return;
        }


    }

    if($('divForm')!= undefined)
    {
        $('divForm').observe("click", AddSuggestListenerInvoice);
    }
});

function regresarVentas(){
    alert("hola");
    window.location=WEB_ROOT+"/reporte-ventas";


}

function EnviarEmail(id){

    new Ajax.Request(WEB_ROOT+'/ajax/manage-facturas.php',
        {
            method:'post',
            parameters: {type: 'enviar_email', id_comprobante: id},
            onSuccess: function(transport){
                var response = transport.responseText || "no response text";
                console.log(response);
                //	alert(response);
                var splitResponse = response.split("[#]");
                if(splitResponse[0] == "ok"){
                    ShowStatusPopUp(splitResponse[1])
                }

            },
            onFailure: function(){ alert('Something went wrong...') }
        });

}//EnviarEmail


