var AJAX_PATH = WEB_ROOT+"/ajax/inventory.php";
jQ(document).ready(function(){
    jQ('#addResource').on('click',function(){
        jQ('#fview').show();
        jQ.ajax({
            url:AJAX_PATH,
            method:'post',
            data:{id:this.id,type:'openAddResource'},
            success:function (response) {
                FViewOffSet('');
                FViewOffSet(response);
                jQ('#closePopUpDiv').on('click',function(){
                    close_popup();
                });
                jQ('#btnResource').on('click',executeFunResource);
            }
        });
    });
});


jQ(document).on('click',".spanEdit",function(){
    grayOut(true);
    jQ('#fview').show();
    jQ.ajax({
        url:AJAX_PATH,
        method:'post',
        data:{id:this.id,type:'openEditResource'},
        success:function (response) {
            FViewOffSet(response);
            jQ('#closePopUpDiv').on('click',function(){
                close_popup();
            });
            jQ('#btnResource').on('click',executeFunResource);
    }

    });
});

jQ(document).on('click',".spanDeleteResponsable",function(){
   var con =  confirm("¿ Esta seguro de realizar esta accion?");
   if(!con)
       return;

    jQ.ajax({
        url:AJAX_PATH,
        method:'post',
        data:{id:this.id,type:'deleteResponsable'},
        success:function (response) {
            var splitResp = response.split("[#]");
            if (splitResp[0] == 'ok') {
                ShowStatusPopUp(splitResp[1]);
                jQ('#div_responsable_resource').html(splitResp[2]);
            }
            else {
                ShowStatusPopUp(splitResp[1]);
            }
        }
    });
});
jQ(document).on('click',"#btnAddResponsable",function(){
    var form = jQ(this).parents('form:first');
    var fd =  new FormData(form[0]);
    fd.set("type","addResponsableToArray");
    jQ.ajax({
        url:AJAX_PATH,
        method:'post',
        data:fd,
        processData: false,
        contentType: false,
        type: 'POST',
        beforeSend: function(){
            jQ('#btnAddResponsable').hide();

        },
        success: function(response){
            jQ('#btnAddResponsable').show();
            var splitResp = response.split("[#]");
            if(splitResp[0]=='ok'){
                ShowStatusPopUp(splitResp[1]);
                jQ('#div_responsable_resource').html(splitResp[2]);
            }
            else{
                jQ('#btnAddResponsable').show();
                ShowStatusPopUp(splitResp[1]);
            }
        }
    });
});

jQ(document).on('change',"#tipo_recurso",function() {
       var selected = jQ(this).children('option:selected').val();
       console.log(selected);
       switch (selected) {
           case 'equipo_computo':
               jQ('.field_computo').show();
           break;
           default:
               jQ('.field_computo').hide();
               break;
       }

});
jQ(document).on('click',".spanDelete",function(){
    grayOut(true);
    jQ('#fview').show();
    jQ.ajax({
        url:AJAX_PATH,
        method:'post',
        data:{id:this.id,type:'openDeleteResource'},
        success:function (response) {
            FViewOffSet(response);
            jQ('#closePopUpDiv').on('click',function(){
                close_popup();
            });
            jQ('#btnResource').on('click',executeFunResource);
        }

    });
});
function executeFunResource(){
    var form = jQ(this).parents('form:first');
    var fd =  new FormData(form[0]);
    jQ.ajax({
        url:AJAX_PATH,
        method:'post',
        data:fd,
        processData: false,
        contentType: false,
        type: 'POST',
        beforeSend: function(){
            jQ("#loading-img").show();
            jQ('#btnResource').hide();
        },
        success: function(response){
            jQ('#btnResource').show();
            jQ("#loading-img").hide();
            var splitResp = response.split("[#]");
            if(splitResp[0]=='ok'){
                ShowStatusPopUp(splitResp[1]);
                jQ('#contenido').html(splitResp[2]);
                close_popup();
            }
            else{
                jQ('#btnResource').show();
                ShowStatusPopUp(splitResp[1]);
            }
        }
    });
}
jQ(document).on('keyup',"#nombre_responsable",function () {
    var time_id = 1;
    var field_value = '';
    field_value = jQ(this).val()
    clearTimeout(time_id);
    time_id = setTimeout(SuggestUser,350);

})
function SuggestUser()
{
    new Ajax.Request(WEB_ROOT+'/ajax/suggest-personal.php',
        {
            parameters: {value: $('nombre_responsable').value},
            method:'post',
            onLoading:function(){
                $('suggestionDivResponsable').hide();
            },
            onSuccess: function(transport){
                var response = transport.responseText || "no response text";
                var splitResponse =  response.split("[#]");
                if(splitResponse[0]=='full'){
                    $('suggestionDivResponsable').show();
                    $('suggestionDivResponsable').innerHTML = splitResponse[1];
                    SuggestListenerPersonal();
                }else{
                    $('suggestionDivResponsable').show();
                    $('suggestionDivResponsable').innerHTML = splitResponse[1];
                }

            },
            onFailure: function(){ alert('Something went wrong...') }
        });
}
var SuggestListenerPersonal = function () {
    var del = jQ(this).hasClass('suggestionResponsable');
    var id = this.id;
    if(del==true){
        FillField(this, id);
        return;
    }
    var del = jQ(this).hasClass('closeSuggestResponsableDiv');
    if(del == true)
    {
        jQ('#suggestionDivResponsable').hide();
        return;
    }
}
function FillField(self,id){
    jQ("#nombre_responsable").val(id);
    jQ('#suggestionDivResponsable').html('');
}
jQ(document).on('click','div#suggestionDivResponsable .suggestionResponsable,div#suggestionDivResponsable .closeSuggestResponsableDiv',SuggestListenerPersonal);


function close_popup(){
    $('fview').innerHTML='';
    $('fview').hide();
    grayOut(false);
    return;
}