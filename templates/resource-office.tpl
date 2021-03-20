<div class="grid_16" id="content">

    <div class="grid_9">
        <h1 class="catalogos">Inventario de recursos de oficina</h1>
    </div>
    <div class="grid_6" id="eventbox">
        {if in_array(255,$permissions)|| $User.isRoot}
            <a style="cursor:pointer" title="Exportar a Excel" onclick="printExcel('contenido')"><img src="{$WEB_ROOT}/images/excel.PNG" width="16" /></a>
            <div id="loadPrint">
            </div>
        {/if}
        {*<a style="cursor:pointer" title="Exportar a excel" ><img src="{$WEB_ROOT}/images/excel.PNG" width="16" /></a>*}
        {if in_array(252,$permissions)|| $User.isRoot}
            <a href="javascript:void(0)" class="inline_add" id="addResource">Agregar</a>
        {/if}

    </div>
    <div class="clear"></div>
    {include file="forms/search-resource-office.tpl"}
    <div id="portlets">
        <div class="clear"></div>
        <div class="portlet">
            <div class="portlet-header">Lista de recursos</div>
            <div class="portlet-content nopadding borderGray" id="contenido" style="overflow: scroll;">
                {include file="lists/resource-office.tpl"}
            </div>
        </div>
    </div>
</div>
