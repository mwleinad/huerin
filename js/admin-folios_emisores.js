var AJAX_PATH = WEB_ROOT+"/ajax/emisores.php";
jQ(document).ready(function () {
    var listenersUpkeep = function () {
        var del = jQ(this).hasClass('spanEdit');
        if(del==true)
            openEditModalEmisor(this);

        var del = jQ(this).hasClass('spanDelete');
        if(del == true)
            deleteEmisor(this);

        var del = jQ(this).hasClass('spanCertificate');
        if(del == true)
            openModalCertificate(this);
    }
    jQ("#contenido").on('click','*',listenersUpkeep);
    jQ("#spanAdd").on('click',openModalAddEmisor);
});
function openEditModalEmisor(self) {
    jQ.ajax({
        url:AJAX_PATH,
        type:"post",
        data:{type:'openEditEmisor',id:self.id},
        success:function (response) {
            grayOut(true);
            $('fview').show();
            FViewOffSet(response);
            jQ('#fviewclose').on('click',close_popup);
            jQ('#btnEmisor').on('click',handlerEmisor);
        },
        error: function(){ alert('Something went wrong...') }
    });
}
function openModalCertificate(self) {
    jQ.ajax({
        url:AJAX_PATH,
        type:"post",
        data:{type:'openModalCertificate',id:self.id},
        success:function (response) {
            grayOut(true);
            $('fview').show();
            FViewOffSet(response);
            jQ('#fviewclose').on('click',close_popup);
            jQ('#btnEmisor').on('click',handlerEmisor);
        },
        error: function(){ alert('Something went wrong...') }
    });
}
function openModalAddEmisor() {
    jQ.ajax({
        url:AJAX_PATH,
        type:"post",
        data:{type:'openModalAddEmisor'},
        success:function (response) {
            grayOut(true);
            $('fview').show();
            FViewOffSet(response);
            jQ('#fviewclose').on('click',close_popup);
            jQ('#btnEmisor').on('click',handlerEmisor);
        },
        error: function(){ alert('Something went wrong...') }
    });
}

function handlerEmisor(){
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
            jQ('#btnEmisor').hide();
        },
        success: function(response){
            jQ("#loading-img").hide();
            jQ('#btnEmisor').show();
            var splitResp = response.split("[#]");
            if(splitResp[0]=='ok'){
                close_popup();
                ShowStatusPopUp(splitResp[1]);
                jQ('#contenido').html(splitResp[2]);
            }
            else{
                jQ('#btnEmisor').show();
                ShowStatusPopUp(splitResp[1]);
            }
        }
    });
}

function deleteEmisor(self) {
    var con = confirm("¿ Esta seguro de realizar esta accion?");
    if (!con)
        return;

    jQ.ajax({
        url: AJAX_PATH,
        method: 'post',
        data: {type: 'deleteEmisor', id:self.id},
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
