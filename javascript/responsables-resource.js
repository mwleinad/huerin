var AJAX_PATH = WEB_ROOT+"/ajax/inventory.php";
jQ(document).ready(function () {
    var listenersResponsables = function () {
        var del = jQ(this).hasClass('spanEdit');
        if(del==true)
            openEditModalResponsable(this);

        var del = jQ(this).hasClass('spanDelete');
        if(del == true)
            openDeleteResponsable(this);
    }
    jQ("#contenido").on('click','*',listenersResponsables);
    jQ(".spanAdd").on('click',openModalResponsable);
});
function openEditModalResponsable(self) {
    jQ.ajax({
        url:WEB_ROOT+"/ajax/inventory.php",
        type:"post",
        data:{type:'openModalResponsable',id:jQ(self).data('resource'),rs_id:self.id},
        success:function (response) {
            grayOut(true);
            $('fview').show();
            FViewOffSet(response);
            jQ('select#personalId').find(':not(:selected)').remove();
            jQ('#fviewclose').on('click',close_popup);
            jQ('#btnResponsable').on('click',handlerResponsable);
        },
        error: function(){ alert('Something went wrong...') }
    });
}
function openModalResponsable() {
    jQ.ajax({
        url:WEB_ROOT+"/ajax/inventory.php",
        type:"post",
        data:{type:'openModalResponsable',id:jQ(this).data('resource')},
        success:function (response) {
            grayOut(true);
            $('fview').show();
            FViewOffSet(response);
            jQ('#fviewclose').on('click',close_popup);
            jQ('#btnResponsable').on('click',handlerResponsable);
        },
        error: function(){ alert('Something went wrong...') }
    });
}

function handlerResponsable(){
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
            jQ('#btnResponsable').hide();
        },
        success: function(response){
            jQ("#loading-img").hide();
            jQ('#btnResponsable').show();
            var splitResp = response.split("[#]");
            if(splitResp[0]=='ok'){
                close_popup();
                ShowStatusPopUp(splitResp[1]);
                jQ('#contenido').html(splitResp[2]);
            }
            else{
                jQ('#btnResponsable').show();
                ShowStatusPopUp(splitResp[1]);
            }
        }
    });
}

function openDeleteResponsable(self) {
    jQ.ajax({
        url:WEB_ROOT+"/ajax/inventory.php",
        type:"post",
        data:{type:'openDeleteResponsable',id:jQ(self).data('resource'),rs_id:self.id},
        success:function (response) {
            grayOut(true);
            $('fview').show();
            FViewOffSet(response);
            jQ('#fviewclose').on('click',close_popup);
            jQ('#btnResponsable').on('click',handlerResponsable);
        },
        error: function(){ alert('Something went wrong...') }
    });
}