<div class="grid_16" id="content">

    <div class="grid_9">
        <h1 class="catalogos">Actividades economicas</h1>
    </div>
    <div class="grid_6" id="eventbox">
        <a style="cursor:pointer" title="Exportar a excel" onclick="ExportRolesDetail('xlsx')"><img src="{$WEB_ROOT}/images/excel.PNG" width="16" /></a>
        {if in_array(273,$permissions)|| $User.isRoot}
            <a href="javascript:void(0)" class="inline_add spanAdd" >Agregar</a>
        {/if}
        <div id="loadPrint">
        </div>
    </div>
    <div class="clear"></div>
    <div id="portlets">
        <div class="clear"></div>
        <div class="portlet">
            <div class="portlet-title">Lista de actividades</div>
            <div class="portlet-content nopadding borderGray" id="contenido">
                {include file="lists/activity.tpl"}
            </div>
        </div>
    </div>
</div>