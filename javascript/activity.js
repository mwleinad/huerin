var AJAX_PATH = WEB_ROOT+"/ajax/activity.php";
jQ(document).ready(function(){

    jQ('.spanAdd').on('click',function(){
        jQ('#fview').show();
        jQ.ajax({
            url:AJAX_PATH,
            method:'post',
            data:{id:this.id,type:'openModalActivity'},
            success:function (response) {
                FViewOffSet('');
                FViewOffSet(response);
                jQ('.select2').select2(ops);
                new Select2Cascade(jQ('#sector'), jQ('#subsector'), WEB_ROOT+"/ajax/load_items_select.php", ops);
                jQ('#closePopUpDiv').on('click',close_popup);
                jQ('#btnControl').on('click',function(){
                    ExecuteFunRol(this);
                });
            }

        });
    });
});
jQ(document).on('click',".spanDelete",function(){
   var con =  confirm("¿ Esta seguro de realizar esta accion?");
   if(!con)
       return;

    jQ.ajax({
        url:AJAX_PATH,
        method:'post',
        data:{id:this.id,type:'delete'},
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
            jQ('.buttonForm').hide();
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
                jQ('#btnControl').show();
                ShowStatusPopUp(splitResp[1]);
            }
        }
    });
}

