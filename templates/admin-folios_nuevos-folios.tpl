<div class="grid_16" id="content">

    <div class="grid_9">
        <h1 class="catalogos">Lista de folios de {$rfcInfo.razonSocial}</h1>
    </div>

    <div class="grid_6" id="eventbox">
        <a style="cursor:pointer" title="Exportar a PDF" onclick="printExcel('contenido','xlsx')"><img src="{$WEB_ROOT}/images/excel.PNG" width="16" /></a>
        <a href="javascript:void(0)" class="inline_add spanAdd" id="{$rfcInfo.rfcId}">Agregar folio</a>
        <div id="loadPrint">
        </div>
    </div>

    <div class="clear">
    </div>

    <div id="portlets">

        <div class="clear"></div>

        <div class="portlet">
            <div class="portlet-header fixed">
                {if $info.limite > 0}
                    Se han utilizado {$info.expedidos} Folios de {$info.limite} disponibles.
                {else}
                    Tu paquete contiene folios ilimitados.
                {/if}
            </div>
            <div class="portlet-content nopadding borderGray" id="contenido">
                {include file="lists/folios.tpl"}
            </div>

        </div>

    </div>
    <div class="clear"></div>

</div>
