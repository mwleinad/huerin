jQ(document).on("click",".spanControlProspect",function () {
    var type =  jQ(this).data('type');
    var id =  jQ(this).data('id');
    jQ.ajax({
        url:WEB_ROOT+"/ajax/prospect.php",
        type:'post',
        data:{ type:type, id:id },
        success:function (response) {
            grayOut(true);
            jQ('#fview').show();
            FViewOffSet(response);
        } ,
        error:function () {
            alert("Error");
        }
    });
});

jQ(document).on('click','.spanSaveProspect',function(){
    var form = jQ(this).parents('form:first');
    if(form.length>0){
        jQ.ajax({
            url:WEB_ROOT+'/ajax/prospect.php',
            method:'post',
            data:form.serialize(true),
            beforeSend:function(){
                jQ('.spanSaveProspect').hide();
                jQ('#loader').show();
            },
            success:function(response){
                var splitResp =  response.split("[#]");
                if(splitResp[0]=='ok'){
                    jQ('#loader').hide();
                    jQ('.spanSaveProspect').show();
                    ShowStatusPopUp(splitResp[1]);
                    jQ('#contenido').html(splitResp[2]);
                    jQ('#fview').hide();
                }
                else{
                    ShowStatusPopUp(splitResp[1]);
                    jQ('#loader').hide();
                    jQ('.spanSaveProspect').show();
                }
            }
        });
    }else
        return;
});

jQ(document).on("click",".spanOpenGetSalario",function () {
    var type =  jQ(this).data('type');
    jQ.ajax({
        url:WEB_ROOT+"/ajax/utilerias.php",
        type:'post',
        data:{type:type},
        success:function (response) {
            grayOut(true);
            jQ('#fview').show();
            FViewOffSet(response);
        } ,
        error:function () {
            alert("Error");
        }
    });
});

jQ(document).on('click','#btnGetSalario',function(){
    var form = jQ(this).parents('form:first');
    if(form.length>0){
        jQ.ajax({
            url:WEB_ROOT+'/ajax/utilerias.php',
            method:'post',
            data:form.serialize(true),
            beforeSend:function(){
                jQ('#btnGetSalario').hide();
                jQ('#loading-img').show();
            },
            success:function(response){
                var splitResp =  response.split("[#]");
                if(splitResp[0]=='ok'){
                    jQ('#loading-img').hide();
                    jQ('#btnGetSalario').show();
                    ShowStatusPopUp(splitResp[1]);
                }
                else{
                    ShowStatusPopUp(splitResp[1]);
                    jQ('#btnGetSalario').show();
                    jQ('#loading-img').hide();
                }
            }
        });
    }else
        return;

});
