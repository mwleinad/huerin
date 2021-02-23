jQ(function () {

    jQ.ajax({
        type: "post",
        url: 'http://chontiq.test/api/auth/login',
        data: { email: "quinn29@hotmail.com", password: "password"},
        dataType: 'json',
    }).done(function (response, status, xhr) {
        var jwt = xhr.getResponseHeader("Authorization");
        console.log(jwt);
        // Store `jwt` in window.localStorage
    }).fail(function (err)  {
        //Error during request
        console.log(err)
    });
})
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

// jquery datatable
// load rows via api ajax
