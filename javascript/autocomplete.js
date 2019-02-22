Event.observe(window, 'load', function()
{
    if($('rfc'))
    {
        var time_id = 1;
        var field_value = '';
        Event.observe($('rfc'), "keyup", function(){
            field_value = this.value
            clearTimeout(time_id);
            if(field_value.length>=3){
                time_id = setTimeout(SuggestUser,350);
            }
        });
    }
    if($('rfc2'))
    {
        var time_id = 1;
        var field_value = '';
        Event.observe($('rfc2'), "keyup", function(){
            field_value = this.value
            clearTimeout(time_id);
            if(field_value.length>=3){
                time_id = setTimeout(SuggestUser2,350);
            }
        });
    }
    AddSuggestListener = function(e) {
        var el = e.element();
        var del = el.hasClassName('suggestUserDiv');
        var id = el.identify();
        if(del == true){
            FillRFC(1, id);
            return;
        }

        del = el.hasClassName('closeSuggestUserDiv');
        if(del == true){
            $('suggestionDiv').hide();
            return;
        }
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
    if($('suggestionDiv')!= undefined)
    {
        $('suggestionDiv').observe("click", AddSuggestListener);
    }
    if($('suggestionDiv2')!= undefined)
    {
        $('suggestionDiv2').observe("click", AddSuggestListener2);
    }
});
function FillRFC(elem, id)
{
    $('suggestionDiv').hide();
    FillDatos(id);
}

function FillRFC2(elem, id)
{
    $('suggestionDiv2').hide();
    FillDatos2(id);
}

function FillDatos(id)
{
    $('loadingDivDatosFactura').innerHTML = '<img src="'+WEB_ROOT+'/images/load.gif" />';
    new Ajax.Request(WEB_ROOT+'/ajax/fill_form_report.php',
        {
            parameters: {value: id, type: "datos"},
            method:'post',
            onSuccess: function(transport){
                var response = transport.responseText || "no response text";
                var splitResponse = response.split("{#}");
                $('rfc').value = splitResponse[0];
                if($('cliente'))
                    $('cliente').value = id;
                $('loadingDivDatosFactura').innerHTML = '';
            },
            onFailure: function(){ alert('Something went wrong...') }
        });
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
                if($('contrato'))
                    $('contrato').value = id;
                $('loadingDivDatosFactura2').innerHTML = '';
            },
            onFailure: function(){ alert('Something went wrong...') }
        });
}

function SuggestUser()
{
    new Ajax.Request(WEB_ROOT+'/ajax/suggest_report.php',
        {
            parameters: {value: $('rfc').value},
            method:'post',
            onLoading:function(){
                if($('cliente'))
                    $('cliente').value = '';
            },
            onSuccess: function(transport){
                var response = transport.responseText || "no response text";
                $('suggestionDiv').show();
                $('suggestionDiv').innerHTML = response;
                AddSuggestListener();
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
            onLoading:function(){
                if($('contrato'))
                    $('contrato').value = '';
            },
            onSuccess: function(transport){
                var response = transport.responseText || "no response text";
                $('suggestionDiv2').show();
                $('suggestionDiv2').innerHTML = response;
                AddSuggestListener2();
            },
            onFailure: function(){ alert('Something went wrong...') }
        });
}