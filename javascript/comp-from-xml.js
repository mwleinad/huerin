Event.observe(window, 'load', function()
{
    if($('rfc2'))
    {
        Event.observe($('rfc2'), "keyup", function(){
          if(this.value==="")
              return;
            SuggestUser2();
            //FillDatosFacturacion();
        });
    }

    AddSuggestListener2 = function(e) {
        var el = e.element();
        var del = el.hasClassName('suggestUserDiv');
        var id = el.identify();
        if(del == true){
            FillRFC2(1, id);
            return;
        }

        del = el.hasClassName('closeSuggestUserDiv');
        if(del == true){
            $('suggestionDiv2').hide();
            return;
        }

    }
    if($('suggestionDiv2')!= undefined)
    {
        $('suggestionDiv2').observe("click", AddSuggestListener2);
    }
});
function FillRFC2(elem, id)
{
    $('suggestionDiv2').hide();
    FillDatos2(id);
}

function FillDatos2(id)
{
    $('loadingDivDatosFactura2').innerHTML = '<img src="'+WEB_ROOT+'/images/load.gif" />';
    new Ajax.Request(WEB_ROOT+'/ajax/fill_form_servicios.php',
        {
            parameters: {value: id, type: "datos"},
            method:'post',
            onSuccess: function(transport){
                var response = transport.responseText || "no response text";
                var splitResponse = response.split("{#}");
                $('rfc2').value = splitResponse[0];
                $('loadingDivDatosFactura2').innerHTML = '';
            },
            onFailure: function(){ alert('Something went wrong...') }
        });
}
function SuggestUser2()
{
    new Ajax.Request(WEB_ROOT+'/ajax/suggest_customer.php',
        {
            parameters: {value: $('rfc2').value},
            method:'post',
            onSuccess: function(transport){
                var response = transport.responseText || "no response text";
                $('suggestionDiv2').show();
                $('suggestionDiv2').innerHTML = response;
                AddSuggestListener2();
            },
            onFailure: function(){ alert('Something went wrong...') }
        });
}


//accion de agregar pago
jQ(document).on('click','.spanAddPayment',function(){
    var name_xml =  jQ(this).data('namexml');
   jQ.ajax({
       url:WEB_ROOT+'/ajax/comp-from-xml.php',
       type: 'POST',
       data:{type:'openAddPaymentFromXml',name_xml:name_xml},
       success: function(response){
           jQ('#fview').show();
           FViewOffSet('');
           FViewOffSet(response);
           jQ('#closePopUpDiv').on('click',function(){
               close_popup();
           });
        }
    })
});
jQ.fn.serializeObject = function() {
    var o = {};
    var a = this.serializeArray();
    jQ.each(a, function() {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};
jQ(document).on('click','#addPayment',function(){
    var id =  this.id;
    var form = jQ(this).parents('form:first');
    var fd =  new FormData(form[0]);
    //enviar formulario de filtro
    var frmFiltro = jQ('#frmSearchFromXml').serializeObject();
    fd.append('frmFiltro',JSON.stringify(frmFiltro));
    jQ.ajax({
        url: WEB_ROOT+'/ajax/add-payment.php',
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        beforeSend: function(){
            jQ('#loading-img').show();
            jQ('#'+id).hide();
        },
        success: function(response){
            var splitResp = response.split("[#]");
            if(splitResp[0]=='ok')
            {
                ShowStatusPopUp(splitResp[1]);
                form[0].reset();
                jQ('#loading-img').hide();
                jQ('#'+id).show();
                jQ('#contenido').html(splitResp[2]);
                jQ('#payments_from_xml').html(splitResp[3]);
                jQ('#mySaldoSpan').html(splitResp[4]);

            }else{
                jQ('#loading-img').hide();
                jQ('#'+id).show();
                ShowStatusPopUp(splitResp[1]);
            }

        },

    });

});
jQ(document).on('click','#btnSearch',function () {
    var id =  this.id;
    var form = jQ(this).parents('form:first');
    var fd =  new FormData(form[0]);
    jQ.ajax({
        url: WEB_ROOT+'/ajax/comp-from-xml.php',
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        beforeSend: function(){
            jQ('#loading-img').show();
            jQ('#'+id).hide();
        },
        success: function(response){
            var splitResp = response.split("[#]");
            jQ('#loading-img').hide();
            jQ('#'+id).show();
            jQ('#contenido').html(splitResp[1]);
        },

    });
});
jQ(document).on('click','.spanDeletePayment',function () {
    var message = "Esta seguro de eliminar el pago";
    if(!confirm(message))
    {
        return;
    }
    var fd = new FormData();
    var frmFiltro = jQ('#frmSearchFromXml').serializeObject();
    fd.append('type','deletePaymentFromXml');
    fd.append('payment_id',this.id);
    fd.append('frmFiltro',JSON.stringify(frmFiltro));

    jQ.ajax({
        url:WEB_ROOT+'/ajax/comp-from-xml.php',
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function(response){
            var splitResp = response.split("[#]");
            ShowStatusPopUp(splitResp[1]);
            jQ('#contenido').html(splitResp[2]);
            jQ('#payments_from_xml').html(splitResp[3]);
            jQ('#mySaldoSpan').html(splitResp[4]);
        }
    })

});
jQ(document).on('click','.spanUpdatePayments',function () {
    var message = "Esta seguro de actualizar los pagos realizados";
    if(!confirm(message))
    {
        return;
    }
    var fd = new FormData();
    fd.append('type','updatePaymentsFromXml');
    jQ.ajax({
        url:WEB_ROOT+'/ajax/comp-from-xml.php',
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        beforeSend:function(){
          jQ('#loadPrint').html('Actualizando pago.....');
        },
        success: function(response){
            var splitResp =  response.split("[#]");
            jQ('#loadPrint').html('');
            ShowStatusPopUp(splitResp[1]);

        }
    })

});
jQ(document).on('click','.spanUploadFacturas',function () {
    var message = "Esta seguro de cargar las facturas";
    if(!confirm(message))
    {
        return;
    }
    var fd = new FormData();
    fd.append('type','uploadInvoiceFromXmlToTable');
    jQ.ajax({
        url:WEB_ROOT+'/ajax/comp-from-xml.php',
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        beforeSend:function(){
            jQ('#loadPrint').html('Actualizando pago.....');
        },
        success: function(response){
            var splitResp =  response.split("[#]");
            jQ('#loadPrint').html('');
            ShowStatusPopUp(splitResp[1]);

        }
    })

});
jQ(document).on('click','.spanMoveFacturasToRealTable',function () {
    var message = "Esta seguro de cargar las facturas";
    if(!confirm(message))
    {
        return;
    }
    var fd = new FormData();
    fd.append('type','moveFacturasToRealTable');
    jQ.ajax({
        url:WEB_ROOT+'/ajax/comp-from-xml.php',
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        beforeSend:function(){
            jQ('#loadPrint').html('Actualizando pago.....');
        },
        success: function(response){
            var splitResp =  response.split("[#]");
            jQ('#loadPrint').html('');
            ShowStatusPopUp(splitResp[1]);

        }
    })

});
jQ(document).on('click','.spanMovePaymentsToRealTable',function () {
    var message = "Esta seguro de cargar los pagos a la tabla real";
    if(!confirm(message))
    {
        return;
    }
    var fd = new FormData();
    fd.append('type','movePaymentsToRealTable');
    jQ.ajax({
        url:WEB_ROOT+'/ajax/comp-from-xml.php',
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        beforeSend:function(){
            jQ('#loadPrint').html('Actualizando pago.....');
        },
        success: function(response){
            var splitResp =  response.split("[#]");
            jQ('#loadPrint').html('');
            ShowStatusPopUp(splitResp[1]);

        }
    })

});
