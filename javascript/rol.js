var AJAX_PATH = WEB_ROOT+"/ajax/rol.php";
jQ(document).ready(function(){
    jQ('.spanConfig').on('click',function(){
        grayOut(true);
        jQ('#fview').show();
        jQ.ajax({
            url:AJAX_PATH,
            method:'post',
            data:{id:this.id,type:'open_config'},
            success:function (response) {
                FViewOffSet(response);
                jQ('div#fview').on('click','#closePopUpDiv',function(){close_popup()});
                jQ('div#fview').on('click','#saveConfig',function(){SaveConfig(this)});
            }

        });
    });

});
jQ('#saveConfigRol').on('click',function(){

});
function close_popup(){
    $('fview').hide();
    grayOut(false);
    return;
}
function SaveConfig(self){
    console.log('res');
    var id =  self.id;
    var form = jQ(self).parents('form:first');
    var fd =  new FormData(form[0]);
    jQ.ajax({
        url:AJAX_PATH,
        method:'post',
        data:fd,
        processData: false,
        contentType: false,
        type: 'POST',
        beforeSend: function(){
            jQ('#saveConfig').hide();

        },
        success: function(response){
            var splitResp = response.split("[#]");
            if(splitResp[0]=='ok'){
                close_popup();
                ShowStatusPopUp(splitResp[1]);
            }
            else{
                jQ('#saveConfig').show();
                ShowStatusPopUp(splitResp[1]);
            }
        }
    });
}