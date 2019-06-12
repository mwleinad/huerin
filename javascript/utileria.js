jQ(document).on("click",".spanDoAction",function () {
   var type =  jQ(this).data('type');
   jQ.ajax({
      url:WEB_ROOT+"/ajax/utilerias.php",
      type:'post',
      data:{type:type},
      success:function (response) {
          var splitResponse =  response.split("[#]");
          ShowStatusPopUp(splitResponse[1]);
      } ,
       error:function () {
           alert("Error al cancelar");
       }
   });
});

jQ(document).on("click",".spanOpenModalCheck",function () {
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
            alert("Error al cancelar");
        }
    });
});

jQ(document).on('click','#btnCheckStatus',function(){
    var form = jQ(this).parents('form:first');
    if(form.length>0){
        jQ.ajax({
            url:WEB_ROOT+'/ajax/utilerias.php',
            method:'post',
            data:form.serialize(true),
            beforeSend:function(){
                jQ('#btnCheckStatus').hide();
                jQ('#loading-img').show();
            },
            success:function(response){
                var splitResp =  response.split("[#]");
                if(splitResp[0]=='ok'){
                    jQ('#loading-img').hide();
                    jQ('#btnCheckStatus').show();
                    ShowStatusPopUp(splitResp[1]);
                }
                else{
                    ShowStatusPopUp(splitResp[1]);
                    jQ('#btnCheckStatus').show();
                    jQ('#loading-img').hide();
                }
            }
        });
    }else
        return;

});