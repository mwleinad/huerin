jQ(document).on('click','.spanEdit',function(){
    jQ.ajax({
       method:'post',
       url:WEB_ROOT+'/ajax/catalogoExpediente.php',
       data:{type:'editExpediente',id:this.id},
       success:function(response){
           grayOut(true);
           jQ('#fview').show();
           FViewOffSet(response);
       }  ,
       error:function(){
           alert('Error!')
       }
    });

});
jQ(document).on('click','.spanDelete',function(){
    jQ.ajax({
        method:'post',
        url:WEB_ROOT+'/ajax/catalogoExpediente.php',
        data:{type:'deleteExpediente',id:this.id},
        success:function(response){
            var splitResp =  response.split("[#]");
            ShowStatusPopUp(splitResp[1]);
            jQ('#contenido').html(splitResp[2]);
        },
        error:function(){
            alert('Error!');
        }
    });
});
jQ(document).on('click','#addExpediente',function(){
    jQ.ajax({
        method:'post',
        url:WEB_ROOT+'/ajax/catalogoExpediente.php',
        data:{type:'addExpediente'},
        success:function(response){
            grayOut(true);
            jQ('#fview').show();
            FViewOffSet(response);
        },
        error:function(){
            alert('Error!');
        }
    });
});

jQ(document).on('click','#btnExpediente',function(){
    var form = jQ(this).parents('form:first');
    if(form.length>0){
        jQ.ajax({
            url:WEB_ROOT+'/ajax/catalogoExpediente.php',
            method:'post',
            data:form.serialize(true),
            beforeSend:function(){
              jQ('#btnExpediente').hide();
              jQ('#loading-img').show();
            },
            success:function(response){
                var splitResp =  response.split("[#]");
                if(splitResp[0]=='ok'){
                    jQ('#loading-img').hide();
                    ShowStatusPopUp(splitResp[1]);
                    jQ('#contenido').html(splitResp[2]);
                    close_popup();
                }
                else{
                    ShowStatusPopUp(splitResp[1]);
                    jQ('#btnExpediente').show();
                    jQ('#loading-img').hide();
                }
            }
        });
    }else
        return;

});

jQ(document).on('click','#closePopUpDiv',function(){
    close_popup();
});