var AJAX_PATH = WEB_ROOT+'/ajax/add-payment.php'
jQ(document).ready(function(){
   jQ('#addPayment').on('click',function(){
        var id =  this.id;
        var form = jQ(this).parents('form:first');
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
                    ShowStatusPopUp(splitResp[1]);
                    form[0].reset();
                    jQ('#loading-img').hide();
                    jQ('#'+id).show();

                }else{
                    jQ('#loading-img').hide();
                    jQ('#'+id).show();
                    ShowStatusPopUp(splitResp[1]);
                }

            },

        });

   });
});
