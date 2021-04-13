<div class="grid_16" id="content">

    <div class="grid_9">
        <h1 class="catalogos">Reporte de inventario</h1>
    </div>
    <div class="grid_6" id="eventbox">
        {if in_array(255,$permissions)|| $User.isRoot}
            <a style="cursor:pointer" title="Exportar a Excel" onclick="printExcelJq('contenido')"><img src="{$WEB_ROOT}/images/excel.PNG" width="16" /></a>
            <div id="loadPrint">
            </div>
        {/if}
    </div>
    <div class="clear"></div>
    {include file="forms/search-resource-office.tpl"}
    <div id="portlets">
        <div class="clear"></div>
        <div class="portlet">
            <div class="portlet-header">Reporte</div>
            <div class="portlet-content nopadding borderGray" id="contenido">

            </div>
        </div>
    </div>
</div>
