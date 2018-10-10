var UITree = function(){
    var eventClickTasks = function (element,contenedor) {
        jQ(element).on('select_node.jstree', function(e,data){
            e.preventDefault();
            var currentPadre = data.selected;
            var link = jQ("#"+currentPadre).find('a');
            if(link.attr("href")=="javascript:;"){
                var datos =  jQ("#"+currentPadre).data('datos');
                var form = new FormData();
                jQ.each(datos,function (key,value) {
                    form.append(key,value);
                })
                jQ.ajax({
                    url:WEB_ROOT+'/ajax/workflow.php',
                    data:form,
                    type: 'POST',
                    processData: false,
                    contentType:false,
                    beforeSend:function(){
                      jQ(contenedor).html("");
                      jQ(contenedor).html("<img src='"+WEB_ROOT+"/images/loadingDrill.gif' />");
                    },
                    success:function (response) {
                        jQ(contenedor).html(response);
                        createDropzoneDrill("#frm-workflow","contenedor2",{url:AJAX_PATH},data.node.parent);
                    }

                });
               return false;
           }
        });
    }
    return {
      init:function () {

      },
      eventClickTasks:function (ele,contenedor) {
           eventClickTasks(ele,contenedor);
       }
    };

}();
