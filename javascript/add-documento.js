var AJAX_PATH = WEB_ROOT+'/ajax/add-documento.php'
jQ(document).ready(function(){
    if(jQ('.addDocumento').length){
        jQ('.addDocumento').on('click',function() {
             var id =  this.id;
             grayOut(true);
             jQ('#fview').show();
            jQ.ajax({
                url: AJAX_PATH,
                data: {type:'openAddDocumento',contractId:id},
                type: 'POST',
                success:function (response) {
                    FViewOffSet('');
                    FViewOffSet(response);
                }

            })
        });
    }
});
jQ(document).on('click','#addDocumento',function(){
    var id = this.id;
    var form = jQ(this).parents('form:first')
    var fd =  new FormData(form[0]);
    jQ.ajax({
        url: AJAX_PATH,
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        beforeSend: function(){
            jQ('#loading-img').show();
            jQ('#'+id).hide();
        },
        success: function(response){
            var splitResp = response.split("[#]");
            console.log(response);
            if(splitResp[0]=='ok')
            {
                jQ('#contentDocumentos').html(splitResp[2]);
                ShowStatusPopUp(splitResp[1]);
                close_popup();
            }else{
                jQ('#loading-img').hide();
                jQ('#'+id).show();
                ShowStatusPopUp(splitResp[1]);
            }
        },
    });

});
jQ(document).on('click','#closePopUpDiv',function(){
    close_popup();
});
jQ(document).on('change','#tipoDocumentoId',function(){
    if(this.value==24)
        jQ('#tagExpiration').show();
    else
        jQ('#tagExpiration').hide();

});
function close_popup(){
    $('fview').innerHTML='';
    $('fview').hide();
    grayOut(false);
    return;
}