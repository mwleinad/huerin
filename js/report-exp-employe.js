document.addEventListener("DOMContentLoaded",function (evt) {
    var btnSearch =  document.getElementById("btnSearch");
    if(btnSearch!=null){
        btnSearch.addEventListener("click",Search)
    }
    var btnExportExcel =  document.getElementById("btnExportExcel");
    if(btnExportExcel!=null){
        btnExportExcel.addEventListener("click",function (ev) {
            if(document.getElementById('frmSearch')!=null){
                document.getElementById('frmSearch').submit();
            }
        });
    }
    function Search(){
        var element = jQ(this).parents("form:first");
        var form = element[0];
        var dataFormat =  new FormData(form);
        jQ.ajax({
          url:WEB_ROOT+"/ajax/personal.php",
          type:'POST',
          data:dataFormat,
          contentType:false,
          processData:false,
          beforeSend:function(){
          },
          success:function (response) {
              jQ("#contenido").html(response);
          }
        });
    }
})