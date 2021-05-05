jQ(document).on("click","#btnBuscar",function () {
    var form = jQ(this).parents('form:first');
    var fd =  new FormData(form[0]);
    jQ.ajax({
        url: WEB_ROOT+"/ajax/report-bonos.php",
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        beforeSend: function(){
            jQ('#loading-img').show();
            jQ('#btnBuscar').hide();
        },
        success: function(response){
            var splitResp = response.split("[#]");
            if(splitResp[0]=='ok')
            {
                jQ('#loading-img').hide();
                jQ("#btnBuscar").show();
                window.location = splitResp[1];
            }else{
                jQ('#loading-img').hide();
                jQ("#btnBuscar").show();
            }
        },

    });

});

jQ(document).on("change","#tipoPeriodo",function () {
    var periodo = jQ(this).val();
    if(periodo=='trimestral'){
        jQ("#divMensual").hide();
        jQ('#periodMensual').prop('disabled',true);
        jQ('#periodTrimestral').prop('disabled',false);
        jQ("#divTrimestral").show();
    }else if(periodo=='mensual'){
        jQ("#divMensual").show();
        jQ("#divTrimestral").hide();
        jQ('#periodMensual').prop('disabled',false);
        jQ('#periodTrimestral').prop('disabled',true);
    }

});
