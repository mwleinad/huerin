var AJAX_PATH = WEB_ROOT+"/ajax/manage-folios.php";
jQ(document).ready(function () {
    var listenersFolios = function () {
        var del = jQ(this).hasClass('spanEdit');
        if(del==true)
            openEditModalFolios(this);
    }
    jQ("#contenido").on('click','*',listenersFolios);
    jQ(".spanAdd").on('click',openModalAddFolios);
});
function openEditModalFolios(self) {
    jQ.ajax({
        url:AJAX_PATH,
        type:"post",
        data:{type:'openEditModalFolio',id:self.id,rfcId:jQ(self).data('rfc')},
        success:function (response) {
            grayOut(true);
            $('fview').show();
            FViewOffSet(response);
            jQ('#fviewclose').on('click',close_popup);
            jQ('#btnFolios').on('click',handlerFolios);
        },
        error: function(){ alert('Something went wrong...') }
    });
}
function openModalAddFolios() {
    jQ.ajax({
        url:AJAX_PATH,
        type:"post",
        data:{type:'openModalAddFolio',rfcId:this.id},
        success:function (response) {
            grayOut(true);
            $('fview').show();
            FViewOffSet(response);
            jQ('#fviewclose').on('click',close_popup);
            jQ('#btnFolios').on('click',handlerFolios);
        },
        error: function(){ alert('Something went wrong...') }
    });
}

function handlerFolios(){
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
            jQ('#btnFolios').hide();
        },
        success: function(response){
            jQ("#loading-img").hide();
            jQ('#btnFolios').show();
            var splitResp = response.split("[#]");
            if(splitResp[0]=='ok'){
                close_popup();
                ShowStatusPopUp(splitResp[1]);
                jQ('#contenido').html(splitResp[2]);
            }
            else{
                jQ('#btnFolios').show();
                ShowStatusPopUp(splitResp[1]);
            }
        }
    });
}