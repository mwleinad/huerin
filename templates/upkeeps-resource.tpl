<input type="hidden" name="contractId" id="contractId" value="{$id}" />
<div class="grid_16" id="content">
  <div class="grid_9">
    <h1 class="content_edit">Mantenimientos realizados</h1>
  </div>
  <div class="grid_6" id="eventbox">
      {if in_array(264,$permissions)|| $User.isRoot}
          <a href="javascript:;" title="Agregar" class="spanAll spanAdd" data-resource="{$id}">
              <img src="{$WEB_ROOT}/images/addn.png">
          </a>
      {/if}
  </div>
  <div class="clear"></div>
  <div id="portlets">
  <div class="clear"></div>
  <div class="portlet">
      <div class="portlet-content nopadding borderGray" id="contenido">
          {include file="lists/upkeeps-resource.tpl"}
      </div>
      <div id="loading" align="center" style="display:none">
        <img src="{$WEB_ROOT}/images/loading.gif" />
        <br />
        Cargando...
      </div>
    </div>
 </div>
<div class="clear"></div>
</div>
<div id="fview" style="display:none;">	
      <input type="hidden" id="inputs_changed" value="0" />  	
        <div id="fviewload" style="display:block"><img src="{$WEB_ROOT}/images/load.gif" border="0" /></div>
        <div id="fviewcontent" style="display:none"></div>
        <div id="modal">
            <div id="submodal">
            </div>
        </div>
</div>
<div style="position:relative" id="divStatus"></div>