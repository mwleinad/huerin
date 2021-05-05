//agregar multiple servicio
jQ(document).on('click','#addItemService',function () {
    var frm = jQ(this).parents('form:first');
    var form = new FormData(frm[0]);
    form.set('type','addItemService');
    jQ.ajax({
        url:WEB_ROOT+"/ajax/services.php",
        data:form,
        processData: false,
        contentType: false,
        type: 'POST',
        beforeSend: function(){
            jQ('#addItemService').hide();
        },
        success: function(response){
            var splitResp = response.split("[#]");
            if(splitResp[0]=='ok'){
                jQ('#contenidoItems').html(splitResp[2]);
                jQ('#addItemService').show();
                frm[0].reset();
            }
            else{
                jQ('#addItemService').show();
                ShowStatusPopUp(splitResp[1]);
            }
        }

    });

});
//delete multiple servicio
jQ(document).on('click','.spanDeleteItemService',function () {
    jQ.ajax({
        url:WEB_ROOT+"/ajax/services.php",
        data:{type:'delItemService',id:this.id},
        type: 'POST',
        success: function(response){
            var splitResp = response.split("[#]");
            jQ('#contenidoItems').html(splitResp[2]);
        }
    });
});

function AddService()
{
    new Ajax.Request(WEB_ROOT+'/ajax/services.php',
        {
            method:'post',
            parameters: $('addServicioForm').serialize(true),
            onLoading:function() {
                $('loading-img').style.display='block';
                $('addServiceButton').style.display='none';
            },
            onSuccess: function(transport){
                var response = transport.responseText || "no response text";
                var splitResponse = response.split("[#]");
                if(splitResponse[0] == "fail")
                {
                    $('loading-img').style.display='none';
                    $('addServiceButton').style.display='block';
                    ShowStatusPopUp(splitResponse[1])
                }
                else
                {
                    $('loading-img').style.display='none';
                    $('addServiceButton').style.display='block';
                    ShowStatusPopUp(splitResponse[1])
                    $('contenido').innerHTML = splitResponse[2];
                    close_popup();
                }
            },
            onFailure: function(){ alert('Something went wrong...') }
        });
}