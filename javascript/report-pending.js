jQ(document).on('click','.spanAddPendiente',function (e) {
    jQ.ajax({
        url:WEB_ROOT+"/ajax/report-pending.php",
        type:'post',
        data:{type:'reportPendingPopUp'},
        beforeSend:function(){
        },
        success:function (response) {
            grayOut(true);
            jQ('#fview').show();
            FViewOffSet('');
            FViewOffSet(response);
        }
    })
});
jQ(document).on('click','.spanEdit',function () {
    jQ.ajax({
        url:WEB_ROOT+"/ajax/report-pending.php",
        type:'post',
        data:{type:'editPendingPopUp',id:this.id},
        beforeSend:function(){
        },
        success:function (response) {
            grayOut(true);
            jQ('#fview').show();
            FViewOffSet('');
            FViewOffSet(response);
        }
    })
});
jQ(document).on('click','.spanComment',function () {
    jQ.ajax({
        url:WEB_ROOT+"/ajax/report-pending.php",
        type:'post',
        data:{type:'addCommentPopUp',id:this.id},
        beforeSend:function(){
        },
        success:function (response) {
            grayOut(true);
            jQ('#fview').show();
            FViewOffSet('');
            FViewOffSet(response);
        }
    })
});
jQ(document).on("click","#btnComment",function(){
    var form = jQ(this).parents('form:first');
    var fd =  new FormData(form[0]);
    jQ.ajax({
        url: WEB_ROOT+"/ajax/report-pending.php",
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        beforeSend: function(){
            jQ('#loading-img').show();
            jQ('#btnComment').hide();
        },
        success: function(response){
            var splitResp = response.split("[#]");
            if(splitResp[0]=='ok')
            {
                ShowStatusPopUp(splitResp[1]);
                form[0].reset();
                jQ('#loading-img').hide();
                jQ("#btnComment").show();
                jQ("#commentsPending").html(splitResp[2]);
            }else{
                jQ('#loading-img').hide();
                jQ("#btnComment").show();
                ShowStatusPopUp(splitResp[1]);
            }
        },

    });
})

jQ(document).on('click','.spanDeleteComment',function () {

    var com = confirm("¿ Esta seguro de eliminar este comentario ?");
    if(!com)
        return;

    jQ.ajax({
        url: WEB_ROOT+"/ajax/report-pending.php",
        data: {type:"deleteCommentPending",id:this.id},
        type: 'POST',
        success: function(response){
            var splitResp = response.split("[#]");
            if(splitResp[0]=='ok')
            {
                ShowStatusPopUp(splitResp[1]);
                jQ("#commentsPending").html(splitResp[2]);
            }else{
                ShowStatusPopUp(splitResp[1]);
            }
        },

    });
});
jQ(document).on('click','.spanChangeStatus',function () {

    jQ.ajax({
        url:WEB_ROOT+"/ajax/report-pending.php",
        type:'post',
        data:{type:'changeStatusPopUp',id:this.id},
        beforeSend:function(){
        },
        success:function (response) {
            grayOut(true);
            jQ('#fview').show();
            FViewOffSet('');
            FViewOffSet(response);
        }
    })
});
jQ(document).on("click","#btnChange",function(){
    var form = jQ(this).parents('form:first');
    var fd =  new FormData(form[0]);
    jQ.ajax({
        url: WEB_ROOT+"/ajax/report-pending.php",
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        beforeSend: function(){
            jQ('#loading-img').show();
            jQ('#btnPending').hide();
        },
        success: function(response){
            var splitResp = response.split("[#]");
            if(splitResp[0]=='ok')
            {
                ShowStatusPopUp(splitResp[1]);
                form[0].reset();
                jQ('#loading-img').hide();
                jQ("#btnPending").show();
                jQ("#contenido").html(splitResp[2]);
                close_popup();
            }else{
                jQ('#loading-img').hide();
                jQ("#btnPending").show();
                ShowStatusPopUp(splitResp[1]);
            }
        },

    });
});
jQ(document).on("click","#btnPending",function(){
        var form = jQ(this).parents('form:first');
        var fd =  new FormData(form[0]);
        jQ.ajax({
            url: WEB_ROOT+"/ajax/report-pending.php",
            data: fd,
            processData: false,
            contentType: false,
            type: 'POST',
            beforeSend: function(){
                jQ('#loading-img').show();
                jQ('#btnPending').hide();
            },
            success: function(response){
                var splitResp = response.split("[#]");
                if(splitResp[0]=='ok')
                {
                    ShowStatusPopUp(splitResp[1]);
                    form[0].reset();
                    jQ('#loading-img').hide();
                    jQ("#btnPending").show();
                    jQ("#contenido").html(splitResp[2]);
                    close_popup();
                }else{
                    jQ('#loading-img').hide();
                    jQ("#btnPending").show();
                    ShowStatusPopUp(splitResp[1]);
                }
            },

        });
});
jQ(document).on("click",".spanDelete",function(){
    jQ.ajax({
        url: WEB_ROOT+"/ajax/report-pending.php",
        data: {type:"deletePending",id:this.id},
        type: 'POST',
        success: function(response){
            var splitResp = response.split("[#]");
            if(splitResp[0]=='ok')
            {
                ShowStatusPopUp(splitResp[1]);
                jQ("#contenido").html(splitResp[2]);

            }else{
                ShowStatusPopUp(splitResp[1]);
            }
        },

    });
});

