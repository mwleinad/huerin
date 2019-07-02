Event.observe(window, 'load', function()
{
    if($('rfc'))
    {
        Event.observe($('rfc'), "keyup", function(){
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
    if($('divForm')!= undefined)
    {
        $('divForm').observe("click", AddSuggestListener);
    }
});
function FillDatos(id)
{
    new Ajax.Request(WEB_ROOT+'/ajax/fill_form_report.php',
        {
            parameters: {value: id, type: "datos"},
            method:'post',
            onSuccess: function(transport){
                var response = transport.responseText || "no response text";
                var splitResponse = response.split("{#}");
                $('rfc').value = splitResponse[0];
                $('cliente').value = id;
            },
            onFailure: function(){ alert('Something went wrong...') }
        });
}
function FillRFC(elem, id)
{
    $('suggestionDiv').hide();
    FillDatos(id);
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
function doSearch(){
	new Ajax.Request(WEB_ROOT+'/ajax/report-cobranza.php',
	{
		method:'post',
		parameters: $('frmSearch').serialize(true),
		onLoading: function(){
			$("loading").style.display = "block";
			$("contenido").style.display = "none";
			$('totalRegs').innerHTML = "";
		},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			
			var splitResponse = response.split("[#]");
			if(splitResponse[0] == "fail")
			{
				ShowStatusPopUp(splitResponse[1])
			}
			else
			{
				$("loading").style.display = "none";
				$("contenido").style.display = "block";
				$('contenido').innerHTML = splitResponse[1];
				$('totalRegs').innerHTML = splitResponse[2];
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
	
}
jQ(document).on('click','#btnSearch',function(){
	var form =  jQ(this).parents('form:first');
	jQ.ajax({
		url:WEB_ROOT+"/ajax/report-cobranza.php",
		type:'post',
		data:form.serialize(true),
        beforeSend:function(){
		  jQ('#loading-img').show();
          jQ('#btnSearch').hide();
        },
		success:function (response) {
			var res =  response.split("[#]");
            jQ('#loading-img').hide();
            jQ('#btnSearch').show();
            if(res[0]=='ok'){
				jQ('#contenido').html(res[1]);
			}else{
                jQ('#loading-img').hide();
                jQ('#btnSearch').show();
				ShowStatusPopUp(res[1]);
			}
        },
        error:function (error) {
			alert(error);
        }
	});
});
function ExportRepServBono()
{
    var resp = confirm("Esta seguro de generar este reporte? El proceso puede tardar varios minutos.");
    if(!resp)
        return;
    $('frmSearch').submit(); return true;
}