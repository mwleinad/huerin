<div class="grid_16" id="content">

    <div class="grid_9">
        <h1 class="catalogos">Roles</h1>
    </div>
    <div class="grid_6" id="eventbox">
        {*<a style="cursor:pointer" title="Exportar a PDF" onclick="ExportRolesDetail('pdf')"><img src="{$WEB_ROOT}/images/pdf_icon.png" width="16" /></a>*}
        <a style="cursor:pointer" title="Exportar a PDF" onclick="ExportRolesDetail('xlsx')"><img src="{$WEB_ROOT}/images/excel.PNG" width="16" /></a>
        {if in_array(112,$permissions) || $User.isRoot}
        <a href="javascript:void(0)" class="inline_add" id="addRol">Agregar Rol</a>
        {/if}
        <div id="loadPrint">
        </div>
    </div>
    <div class="clear"></div>
    <div id="portlets">
        <div class="clear"></div>
        <div class="portlet">
            <div class="portlet-title">Lista de roles</div>
            <div class="portlet-content nopadding borderGray" id="contenido">
                {include file="lists/rol.tpl"}
            </div>
        </div>
    </div>
    {if in_array(226,$permissions) || $User.isRoot}
    <div class="grid_6" id="eventboxLeft">
        {if in_array(227,$permissions) || $User.isRoot}
        <a href="javascript:void(0)" class="inline_add" id="addPorcentBono" title="Agregar nivel y %">Agregar</a>
        <div id="loadPrint">
        </div>
        {/if}
    </div>
    <div class="clear"></div>
    <div id="portlets">
        <div class="portlet">
            <div class="portlet-title">Porcentaje de bonos por categoria</div>
            <div class="portlet-content nopadding borderGray" id="contenidoPorcentBono">
                {include file="lists/porcent-bonos.tpl"}
            </div>
        </div>
    </div>
    {/if}
</div>