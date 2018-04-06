var AJAX_PATH = WEB_ROOT+"/ajax/rol.php";
jQ(document).ready(function(){

    jQ('#addRol').on('click',function(){
        grayOut(true);
        jQ('#fview').show();
        jQ.ajax({
            url:AJAX_PATH,
            method:'post',
            data:{id:this.id,type:'addRol'},
            success:function (response) {
                FViewOffSet(response);
                jQ('div#fview').on('click','#closePopUpDiv',function(){close_popup()});
                jQ('div#fview').on('click','#btnRol',function(){
                    ExecuteFunRol(this)});
            }

        });
    });


});

jQ(document).on('click','#copyPermiso',function(){
    var rolId = jQ('#id').val();
    var baseId = jQ('#rolBaseId').val();
    jQ.ajax({
        url:AJAX_PATH,
        method:'post',
        data:{id:rolId,baseId:baseId,type:'copyPermiso'},
        success:function (response) {
            jQ('#det-config').html(response);
            jQ('div#fview').on('click','#saveConfig',function(){SaveConfig(this)});
            TogglePermisos();
        }

    });
});
jQ(document).on('click',".spanConfig",function(){
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
            TogglePermisos();
        }

    });
});
jQ(document).on('click',".spanEdit",function(){
    grayOut(true);
    jQ('#fview').show();
    jQ.ajax({
        url:AJAX_PATH,
        method:'post',
        data:{id:this.id,type:'editRol'},
        success:function (response) {
            FViewOffSet(response);
            jQ('div#fview').on('click','#closePopUpDiv',function(){close_popup()});
            jQ('div#fview').on('click','#btnEdit',function(){ExecuteFunRol(this)});
        }

    });
});
jQ(document).on('click',".spanDelete",function(){
   var con =  confirm("¿ Esta seguro de realizar esta accion?");
   if(!con)
       return;

    jQ.ajax({
        url:AJAX_PATH,
        method:'post',
        data:{id:this.id,type:'deleteRol'},
        success:function (response) {
            var splitResp = response.split("[#]");
            if (splitResp[0] == 'ok') {
                ShowStatusPopUp(splitResp[1]);
                jQ('#contenido').html(splitResp[2]);
            }
            else {
                ShowStatusPopUp(splitResp[1]);
            }
        }
    });
});

function close_popup(){
    $('fview').hide();
    grayOut(false);
    return;
}
function SaveConfig(self){
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
function TogglePermisos(){
    jQ('.deepList').on('click',function(){
        if(jQ("ul#"+this.id).is(':visible')){
            jQ("#"+this.id).html('[+]-');
            jQ("ul#"+this.id).removeClass('siShow');
        }
        else
        {
            jQ('#'+this.id).html('[-]-');
            jQ("ul#"+this.id).addClass('siShow');
        }

    });
}
function ExecuteFunRol(self){
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
            jQ('#btnRol').hide();
            jQ('#loading-img').show();

        },
        success: function(response){
            var splitResp = response.split("[#]");
            if(splitResp[0]=='ok'){
                close_popup();
                ShowStatusPopUp(splitResp[1]);
                jQ('#contenido').html(splitResp[2]);
            }
            else{
                jQ('#loading-img').hide();
                jQ('#btnRol').show();
                ShowStatusPopUp(splitResp[1]);
            }
        }
    });
}