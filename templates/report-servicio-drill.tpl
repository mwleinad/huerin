<div class="grid_16" id="content">
  <div class="grid_9">
    <h1 class="reportes">Administrador de archivos</h1>
  </div>
  <div class="grid_6" id="eventbox">
  <div id="loadPrint">
  </div>
  </div>
  <div class="clear">
  </div>
  
  <div id="portlets">

  <div class="clear"></div>
  
  <div class="portlet">
       {include file="forms/search-report-servicio.tpl"}
       <br />
       <div align="center" id="loading" style="display:none">
       		<img src="{$WEB_ROOT}/images/loading.gif" />
            <br />
            Cargando...
            <br />&nbsp;
       </div>
      <!--<form class="dropzone" id="frmFileSp"><input type="submit" value="btn"></form>-->
      <div class="portlet-content nopadding borderGray">
         <div style="text-align:center" id="msg-advertencia"><b>Este reporte puede tardar varios minutos si no eliges un cliente. Por favor sea paciente.</b></div>
         <div id="contenido" style="width:50%; float:left" class="tree-demo jstree jstree-1 jstree-default">
             {* include file="lists/report-servicio-level-one.tpl" *}
         </div>
          <div id="contenido2" style="width:50%; float: right; border-left: 1px; display: block ">

          </div>
      </div>
      <div style="clear:both"></div>
        <br />
      
      {include file="{$DOC_ROOT}/templates/boxes/report-walmart-status.tpl"}
                   
    </div>
	    
 </div>
  <div class="clear"> </div>
 
</div>