<div class="grid_16" id="content">
    <div class="grid_9">
        <h1 class="catalogos">Razones sociales</h1>
    </div>
    <div class="grid_6" id="eventbox">
        <a style="cursor:pointer" title="Actualizar pagos desde xml" class="spanAll spanUpdatePayments"><img src="{$WEB_ROOT}/images/icons/update_payments.png" width="16" /></a>
        <div id="loadPrint">
        </div>
    </div>
    <div class="clear"></div>
    <div id="portlets">
        {include file="forms/search-razon-social.tpl"}
        <div class="clear"></div>
        <div class="portlet">
            <div class="portlet-content nopadding borderGray" id="contenido">
                {*include file="lists/comp-from-xml.tpl"*}
            </div>
        </div>
    </div>
    <div class="clear"> </div>
</div>