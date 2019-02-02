<div class="grid_16" id="content">

    <div class="grid_9">
        <h1 class="catalogos">Comprobante de pago desde xml</h1>
    </div>
    <div class="grid_6" id="eventbox">
        <a style="cursor:pointer" title="Actualizar pagos desde xml" class="spanAll spanUpdatePayments"><img src="{$WEB_ROOT}/images/icons/update_payments.png" width="16" /></a>
        <!--<a style="cursor:pointer" title="Cargar facturas a tabla" class="spanAll spanUploadFacturas"><img src="{$WEB_ROOT}/images/icons/iconUp.png" width="16" /></a>
        <a style="cursor:pointer" title="Cargar facturas a tabla" class="spanAll spanMoveFacturasToRealTable"><img src="{$WEB_ROOT}/images/icons/backup_16x16.png" width="16" /></a>
        <a style="cursor:pointer" title="Cargar pagos a tabla" class="spanAll spanMovePaymentsToRealTable"><img src="{$WEB_ROOT}/images/icons/action_check.gif" width="16" /></a>-->
        <div id="loadPrint">
        </div>
    </div>
    <div class="clear"></div>
    <div id="portlets">
        {include file="forms/search-comp-from-xml.tpl"}
        <div class="clear"></div>
        <div class="portlet">
            <div class="portlet-content nopadding borderGray" id="contenido">
                {*include file="lists/comp-from-xml.tpl"*}
            </div>
        </div>
    </div>
    <div class="clear"> </div>
</div>