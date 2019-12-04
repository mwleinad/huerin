var AJAX_PATH = WEB_ROOT+"/ajax/rol.php";
jQ(document).ready(function(){

    jQ('#addRol').on('click',function(){
        jQ('#fview').show();
        jQ.ajax({
            url:AJAX_PATH,
            method:'post',
            data:{id:this.id,type:'addRol'},
            success:function (response) {
                FViewOffSet('');
                FViewOffSet(response);
                jQ('#closePopUpDiv').on('click',function(){
                    close_popup();
                });
                jQ('#btnRol').on('click',function(){
                    ExecuteFunRol(this);
                });
            }

        });
    });
    jQ('#addPorcentBono').on('click',function(){
        jQ('#fview').show();
        jQ.ajax({
            url:AJAX_PATH,
            method:'post',
            data:{id:this.id,type:'addPorcentBono'},
            success:function (response) {
                FViewOffSet('');
                FViewOffSet(response);
                jQ('#closePopUpDiv').on('click',function(){
                    close_popup();
                });
                jQ('#btnPorcent').on('click',function(){
                    ExecuteFunPorcent(this);
                });
            }

        });
    });


});

jQ(document).on('click','#copyPermiso',function(){
    var rolId = jQ('#id').val();
    var baseId = jQ('#rolBaseId').val();
    jQ.ajax({
        url:AJAX_PATH,
        method:'post',
        data:{id:rolId,baseId:baseId,type:'copyPermiso'},
        success:function (response) {
            jQ('#det-config').html(response);
            jQ('#saveConfig').on('click',function(){
                SaveConfig(this);
            });
            TogglePermisos();
        }

    });
});
jQ(document).on('click',".spanConfig",function(){
    grayOut(true);
    jQ('#fview').show();
    jQ.ajax({
        url:AJAX_PATH,
        method:'post',
        data:{id:this.id,type:'open_config'},
        success:function (response) {
            FViewOffSet(response);
            jQ('div#fview').on('click','#closePopUpDiv',function(){close_popup();});
            jQ('#saveConfig').on('click',function(){
                SaveConfig(this);
            });
            TogglePermisos();
        }

    });
});
jQ(document).on('click',".spanEdit",function(){
    grayOut(true);
    jQ('#fview').show();
    jQ.ajax({
        url:AJAX_PATH,
        method:'post',
        data:{id:this.id,type:'editRol'},
        success:function (response) {
            FViewOffSet(response);
            jQ('#closePopUpDiv').on('click',function(){
                close_popup();
            });
            jQ('#btnEdit').on('click',function(){
                ExecuteFunRol(this);
            });
        }

    });
});
jQ(document).on('click',".spanEditPorcent",function(){
    grayOut(true);
    jQ('#fview').show();
    jQ.ajax({
        url:AJAX_PATH,
        method:'post',
        data:{id:this.id,type:'editPorcent'},
        success:function (response) {
            FViewOffSet(response);
            jQ('#closePopUpDiv').on('click',function(){
                close_popup();
            });
            jQ('#btnPorcent').on('click',function(){
                ExecuteFunPorcent(this);
            });
        }

    });
});
jQ(document).on('click',".spanDelete",function(){
   var con =  confirm("¿ Esta seguro de realizar esta accion?");
   if(!con)
       return;

    jQ.ajax({
        url:AJAX_PATH,
        method:'post',
        data:{id:this.id,type:'deleteRol'},
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
jQ(document).on('click',".spanDeletePorcent",function(){
    var con =  confirm("¿ Esta seguro de realizar esta accion?");
    if(!con)
        return;

    jQ.ajax({
        url:AJAX_PATH,
        method:'post',
        data:{id:this.id,type:'deletePorcent'},
        success:function (response) {
            var splitResp = response.split("[#]");
            if (splitResp[0] == 'ok') {
                ShowStatusPopUp(splitResp[1]);
                jQ('#contenidoPorcentBono').html(splitResp[2]);
            }
            else {
                ShowStatusPopUp(splitResp[1]);
            }
        }
    });
});

function close_popup(){
    $('fview').innerHTML='';
    $('fview').hide();
    grayOut(false);
    return;
}
function SaveConfig(self){
    var id =  self.id;
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
            jQ("#loader").show();
            jQ('#saveConfig').hide();
        },
        success: function(response){
            var splitResp = response.split("[#]");
            if(splitResp[0]=='ok'){
                close_popup();
                ShowStatusPopUp(splitResp[1]);
            }
            else{
                jQ("#loader").hide();
                jQ('#saveConfig').show();
                ShowStatusPopUp(splitResp[1]);
            }
        }
    });
}
function TogglePermisos(){
    jQ('.deepList').on('click',function(){
        if(jQ("ul#"+this.id).is(':visible')){
            jQ("#"+this.id).html('[+]-');
            jQ("ul#"+this.id).removeClass('siShow');
        }
        else
        {
            jQ('#'+this.id).html('[-]-');
            jQ("ul#"+this.id).addClass('siShow');
        }

    });
}
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
                jQ('#buttonForm').show();
                ShowStatusPopUp(splitResp[1]);
            }
        }
    });
}
function ExecuteFunPorcent(self){
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
            jQ('#btnPorcent').hide();
            jQ('#loading-img').show();

        },
        success: function(response){
            var splitResp = response.split("[#]");
            if(splitResp[0]=='ok'){
                close_popup();
                ShowStatusPopUp(splitResp[1]);
                jQ('#contenidoPorcentBono').html(splitResp[2]);
            }
            else{
                jQ('#loading-img').hide();
                jQ('#btnPorcent').show();
                ShowStatusPopUp(splitResp[1]);
            }
        }
    });
}
function ExportRolesDetail(tipo){
    jQ.ajax({
        url:AJAX_PATH,
        method:'post',
        data:{type:'export',tipo:tipo},
        type: 'POST',
        beforeSend: function(){
            jQ('#loadPrint').html('Generando reporte......')
        },
        success: function(response){
            jQ('#loadPrint').html('')
            window.location = response;
        }
    });
}
jQ(document).on('change','form#frmPermisos input[type="checkbox"]',function(){
    if(jQ(this).is(':checked')){
        var clss = this.getAttribute('class');
        var clses = clss.split(' ');
        var father = clses[0].split('-');
        if(clses[1])
           var son = clses[1].split('-');
        jQ('form#frmPermisos .child-'+father[1]).each(function(){
            var dclss = this.getAttribute('class');
            var dcses = dclss.split(' ');
            var dfather = dcses[0].split('-');
            this.checked=true;
            if(jQ('.child-'+dfather[1]).length>0)
            {
               deepChecked(dclss,true);
            }
        });
        if(clses[1]){
            //console.log(clses[1]);
            jQ('.father-'+son[1]).prop('checked',true);
            //ir al ultimo nivel
            var ffirst = document.getElementsByClassName('father-'+son[1])[0].getAttribute('class');
            var arrayFirst = ffirst.split(' ');
            if(arrayFirst[1])
               checkUpLevel(arrayFirst[1],true);
        }

    }else{
        var clss = this.getAttribute('class');
        var clses = clss.split(' ');
        var father = clses[0].split('-');
        if(clses[1])
        var son = clses[1].split('-');
        jQ('form#frmPermisos .child-'+father[1]).each(function(){
            var dclss = this.getAttribute('class');
            var dcses = dclss.split(' ');
            var dfather = dcses[0].split('-');
            this.checked=false;
            if(jQ('.child-'+dfather[1]).length>0)
            {
                deepChecked(dclss,false);
            }
        });
        //verificar si al menos uno de los hijos esta activo.
        if(clses[1]){
                 var broActives=0;
                jQ('form#frmPermisos .'+clses[1]).each(function(){
                           if(jQ(this).is(':checked'))
                               broActives++;
                });
                if(broActives<=0){
                    jQ('.father-'+son[1]).prop('checked',false);
                    //ir al ultimo nivel
                    var ffirst = document.getElementsByClassName('father-'+son[1])[0].getAttribute('class');
                    var arrayFirst = ffirst.split(' ');
                    if(arrayFirst[1])
                        checkUpLevelClean(arrayFirst[1],false);
                }

        }
    }
});
function deepChecked(dclss,flag){
    var fchild = dclss.split(' ');
    var dfather = fchild[0].split('-');
    jQ('.child-'+dfather[1]).each(function(){
        var dclss = this.getAttribute('class');
        var dcses = dclss.split(' ');
        var dfather = dcses[0].split('-');
        this.checked=flag;
        if(jQ('.child-'+dfather[1]).length>0)
        {
            deepChecked(dclss,flag);
        }
    });
}
function checkUpLevel(clase,flag){
    //ir al ultimo nivel
    var son = clase.split('-');
    jQ('.father-'+son[1]).prop('checked',true);
    var ffirst =document.getElementsByClassName('father-'+son[1])[0].getAttribute('class');
    var arrayFirst = ffirst.split(' ');
    if(arrayFirst[1])
        checkUpLevel(arrayFirst[1],flag);

}
function checkUpLevelClean(clase,flag){
    //ir al ultimo nivel
    var son = clase.split('-');
    var broActives=0;
    jQ('form#frmPermisos .'+clase).each(function(){
        if(jQ(this).is(':checked'))
            broActives++;
    });
    if(broActives<=0){
        jQ('.father-'+son[1]).prop('checked',false);
        //ir al ultimo nivel
        var ffirst = document.getElementsByClassName('father-'+son[1])[0].getAttribute('class');
        var arrayFirst = ffirst.split(' ');
        if(arrayFirst[1])
            checkUpLevelClean(arrayFirst[1],false);
    }

}