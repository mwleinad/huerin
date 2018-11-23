<div class="grid_16" id="content">
  <div class="grid_9">
  <h1 class="catalogos">Menus del dia</h1>
  </div>
  <div class="grid_6" id="eventbox">
      {if in_array(214,$permissions)|| $User.isRoot}
            <a href="javascript:void(0)" class="inline_add" id="addMenu">Agregar menu</a>
      {/if}
  <div id="loadPrint">
  </div>
  </div>
  <div class="clear">
  </div>
  <div id="portlets">
  <div class="clear"></div>
  <div class="portlet">
      <div class="portlet-content nopadding borderGray" id="contenido" style="overflow-x: scroll">
          {include file="lists/coffe.tpl"}
      </div>
    </div>
 </div>
  <div class="clear"> </div>
</div>