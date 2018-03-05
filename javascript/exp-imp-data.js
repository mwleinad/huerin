var AJAX_PATH = WEB_ROOT+'/ajax/ei-upload.php'
jQ(document).ready(function(){

    jQ('#btnRun').on('click',function() {
        var id = this.id;
        var form = jQ(this).parents('form:first');
        var fd = new FormData(form[0]);
        jQ.ajax({
            url: AJAX_PATH,
            data: fd,
            processData: false,
            contentType: false,
            type: 'POST',
            beforeSend: function () {
                jQ('#loading-img').show();
                jQ('#' + id).hide();
            },
            success: function (response) {
                console.log(response);
                jQ('#' + id).show();
                var splitResp = response.split("[#]");
                if(splitResp[0]=='ok'){
                       jQ('#loading-img').hide();
                      jQ('#contenido').html(splitResp[1]);
                }
                else{
                    jQ('#loading-img').hide();
                    ShowStatusPopUp(splitResp[1]);
                }


            },
            error: function () {
                jQ('#' + id).show();
                alert('error')
            }
        });
    });

});