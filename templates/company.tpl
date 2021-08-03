<div class="grid_16" id="content">
    <div class="grid_9">
        <h1 class="clientes">Empresas de prospecto</h1>
    </div>
    <div class="grid_6" id="eventbox">
        <a href="{$WEB_ROOT}/prospect" class="backbutton">Regresar</a>
        <a href="javascript:void(0)" class="inline_add spanControlCompany"  data-type="openAddCompany"
            data-prospect="{$prospect.id}">Agregar</a>
        <div id="loadPrint">
        </div>
    </div>
    <div class="clear">
    </div>
    <div id="portlets">
        <div class="clear"></div>
        <div class="portlet">
            <input type="hidden" id="customer" name="customer" value="{$prospect.customer_id}"/>
            <input type="hidden" id="prospect_id" name="prospect_id" value="{$prospect.id}"/>
            <div class="portlet-content nopadding borderGray" id="contenido">
                {include file="lists/company.tpl"}
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>
