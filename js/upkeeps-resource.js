var AJAX_PATH = WEB_ROOT+"/ajax/inventory.php";
jQ(document).ready(function () {
    var listenersUpkeep = function () {
        var del = jQ(this).hasClass('spanEdit');
        if(del==true)
            openEditModalUpkeep(this);

        var del = jQ(this).hasClass('spanDelete');
        if(del == true)
            deleteUpkeep(this);
    }
    jQ("#contenido").on('click','*',listenersUpkeep);
    jQ(".spanAdd").on('click',openModalUpkeep);
});
function openEditModalUpkeep(self) {
    jQ.ajax({
        url:WEB_ROOT+"/ajax/inventory.php",
        type:"post",
        data:{type:'openModalUpkeep',id:jQ(self).data('resource'),upk_id:self.id},
        success:function (response) {
            grayOut(true);
            $('fview').show();
            FViewOffSet(response);
            jQ('#fviewclose').on('click',close_popup);
            jQ('#btnUpkeep').on('click',handlerUpkeep);
        },
        error: function(){ alert('Something went wrong...') }
    });
}
function openModalUpkeep() {
    jQ.ajax({
        url:WEB_ROOT+"/ajax/inventory.php",
        type:"post",
        data:{type:'openModalUpkeep',id:jQ(this).data('resource')},
        success:function (response) {
            grayOut(true);
            $('fview').show();
            FViewOffSet(response);
            jQ('#fviewclose').on('click',close_popup);
            jQ('#btnUpkeep').on('click',handlerUpkeep);
        },
        error: function(){ alert('Something went wrong...') }
    });
}

function handlerUpkeep(){
    var form = jQ(this).parents('form:first');
    var fd =  new FormData(form[0]);
    jQ.ajax({
        url:AJAX_PATH,
        method:'post',
        data:fd,
        processData: false,
        contentType: false,
        type: 'POST',
        beforeSend: function(){
            jQ("#loading-img").show();
            jQ('#btnUpkeep').hide();
        },
        success: function(response){
            jQ("#loading-img").hide();
            jQ('#btnUpkeep').show();
            var splitResp = response.split("[#]");
            if(splitResp[0]=='ok'){
                close_popup();
                ShowStatusPopUp(splitResp[1]);
                jQ('#contenido').html(splitResp[2]);
            }
            else{
                jQ('#btnUpkeep').show();
                ShowStatusPopUp(splitResp[1]);
            }
        }
    });
}

function deleteUpkeep(self) {
    var con = confirm("¿ Esta seguro de realizar esta accion?");
    if (!con)
        return;

    jQ.ajax({
        url: AJAX_PATH,
        method: 'post',
        data: {type: 'deleteUpkeep', id: jQ(self).data('resource'), upk_id: self.id},
        success: function (response) {
            var splitResp = response.split("[#]");
            if (splitResp[0] == 'ok') {
                ShowStatusPopUp(splitResp[1]);
                jQ('#contenido').html(splitResp[2]);
            } else {
                ShowStatusPopUp(splitResp[1]);
            }
        }
    });
}
