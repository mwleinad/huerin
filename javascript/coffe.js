jQ(document).on('click','#addMenu',function () {
    jQ.ajax({
        url:WEB_ROOT+"/ajax/coffe.php",
        type:'post',
        data:{type:'openAddCoffePopup'},
        success:function (response) {
            jQ('#fview').show();
            FViewOffSet(response);
            jQ('#closePopUpDiv').on('click',function(){
                close_popup();
            });
        }

    })
});
jQ(document).on('click','#btnAddPlatillo',function () {
    jQ.ajax(
        {
            url:WEB_ROOT+'/ajax/coffe.php',
            type:'post',
            data:{type:'addPlatillo',name:jQ('#name').val()},
            success:function (response) {
                var sp =  response.split("[#]");
                if(sp[0]=='ok'){
                  jQ('.stack_platillo').html(sp[1]);
                }else{
                    ShowStatusPopUp(sp[1]);
                }
            }
        }

    );
});
jQ(document).on('click','.spanDelete',function () {
    var id = this.id;
    jQ.ajax(
        {
            url:WEB_ROOT+'/ajax/coffe.php',
            type:'post',
            data:{type:'deletePlatillo',id:id},
            success:function (response) {
                var sp =  response.split("[#]");
                if(sp[0]=='ok'){
                    jQ('.stack_platillo').html(sp[1]);
                }else{
                    ShowStatusPopUp(sp[1]);
                }
            }
        }
    );
});
jQ(document).on('click','.spanDown',function () {
    var id = this.id;
    jQ.ajax(
        {
            url:WEB_ROOT+'/ajax/coffe.php',
            type:'post',
            data:{type:'deleteMenu',id:id},
            success:function (response) {
                var sp =  response.split("[#]");
                if(sp[0]=='ok'){
                    ShowStatusPopUp(sp[1]);
                    jQ('#contenido').html(sp[2]);
                }else{
                    ShowStatusPopUp(sp[1]);
                }
            }
        }
    );
});
jQ(document).on('click','#btnMenu',function () {
    jQ.ajax(
        {
            url:WEB_ROOT+'/ajax/coffe.php',
            type:'post',
            data:{type:'saveMenu'},
            beforeSend:function(){
                jQ('#loading-img').show();
                jQ('#btnVistaPrevia').hide();
                jQ('#btnMenu').hide();

            },
            success:function (response) {
                var sp =  response.split("[#]");
                if(sp[0]=='ok'){
                    ShowStatusPopUp(sp[1]);
                    jQ('#contenido').html(sp[2]);
                    close_popup();
                }else{
                    jQ('#loading-img').hide();
                    jQ('#btnVistaPrevia').show();
                    jQ('#btnMenu').show();
                    ShowStatusPopUp(sp[1]);
                }
            }
        }
    )
});