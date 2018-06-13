var AJAX_PATH = WEB_ROOT+'/ajax/import-data.php'
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
                jQ('#' + id).show();
                var splitResp = response.split("[#]");
                if(splitResp[0]=='ok'){
                    jQ('#loading-img').hide();
                    ShowStatusPopUp(splitResp[1]);
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
    if(jQ('#formato').length>0){
        jQ('#formato').change(function(){
            if(this.value=="")
                return;
            var tipo = this.value;
           jQ.ajax({
              method:'post',
              url:WEB_ROOT+'/ajax/exp-imp-layout.php',
              data:{type:tipo} ,
              success:function(response){
                  window.location=response;
              } ,
              error:function () {
                  alert('Error al descargar layout');
              }
           });
        })
    }

});