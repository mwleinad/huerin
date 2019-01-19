Event.observe(window, 'load', function()
{
    if($('rfc'))
    {
        Event.observe($('rfc'), "keyup", function(){
            if(this.value==="")
                return;

            SuggestUser();
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
    if($('suggestionDiv')!= undefined)
    {
        $('suggestionDiv').observe("click", AddSuggestListener);
    }
});
function FillRFC(elem, id)
{
    $('suggestionDiv').hide();
    FillDatos(id);
}
function FillDatos(id)
{
    $('loadingDivDatosFactura').innerHTML = '<img src="'+WEB_ROOT+'/images/load.gif" />';
//	$('suggestionDiv').hide();
    new Ajax.Request(WEB_ROOT+'/ajax/fill_form_report.php',
        {
            parameters: {value: id, type: "datos"},
            method:'post',
            onSuccess: function(transport){
                var response = transport.responseText || "no response text";
                var splitResponse = response.split("{#}");
                $('rfc').value = splitResponse[0];
                $('cliente').value = id;
                $('loadingDivDatosFactura').innerHTML = '';
                //BuscarServiciosActivos();
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
            onSuccess: function(transport){
                var response = transport.responseText || "no response text";
                $('suggestionDiv').show();
                $('suggestionDiv').innerHTML = response;
                AddSuggestListener();
            },
            onFailure: function(){ alert('Something went wrong...') }
        });
}
