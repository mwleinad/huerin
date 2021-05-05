jQ(document).on('click','.spanBackup',function (e) {
    e.preventDefault();
    var name_bd =  jQ(this).data('name');
    jQ.ajax({
        url:WEB_ROOT+"/ajax/backup_system.php",
        type:'post',
        data:{type:'doBackup',name_bd:name_bd},
        beforeSend:function(){
            jQ('#span_'+name_bd).html('Respaldando......');
        },
        success:function (response) {
            var splitResp =  response.split('[#]');
            if(splitResp[0]=='ok'){
                jQ('#span_'+name_bd).html('');
                location.href=splitResp[2];
                ShowStatusPopUp(splitResp[1]);
            }else{
                jQ('#span_'+name_bd).html('');
                ShowStatusPopUp(splitResp[1]);
            }

        }
    })
});