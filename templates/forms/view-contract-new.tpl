<form name="frmContract" id="frmContract" enctype="multipart/form-data" method="post">
<input type="hidden" name="action" value="save" />

	{include file="{$DOC_ROOT}/templates/forms/view-contract-basic.tpl"}



<div id="infoCompraVenta2" style="display:block">
	{include file="{$DOC_ROOT}/templates/forms/view-datos-contacto.tpl"}
</div>

{if $User.roleId < 3}
<div id="infoCompraVenta3" style="display:block">
	{include file="{$DOC_ROOT}/templates/forms/view-fiel.tpl"}
</div>

<div id="infoCompraVenta4" style="display:block">
	{include file="{$DOC_ROOT}/templates/forms/requerimientos.tpl"}
</div>

<div id="infoCompraVenta" style="display:block">
	{include file="{$DOC_ROOT}/templates/forms/documentos.tpl"}
</div>

<div id="infoArrendamiento2" style="display:block">
	{include file="{$DOC_ROOT}/templates/forms/archivos.tpl"}
</div>
<!--
<div id="infoImpuestos" style="display:block">
	{include file="{$DOC_ROOT}/templates/forms/impuestos.tpl"}
</div>

<div id="infoObligaciones" style="display:block">
	{include file="{$DOC_ROOT}/templates/forms/obligaciones.tpl"}
</div>
-->
{else}
<div id="infoCompraVenta4" style="display:block">
	{include file="{$DOC_ROOT}/templates/forms/requerimientos.tpl"}
</div>
<div id="infoCompraVenta" style="display:block">
	{include file="{$DOC_ROOT}/templates/forms/documentos.tpl"}
</div>
<div id="infoArrendamiento2" style="display:block">
	{include file="{$DOC_ROOT}/templates/forms/archivos.tpl"}
</div>
<div id="infoCompraVenta3" style="display:block">
	{include file="{$DOC_ROOT}/templates/forms/view-fiel.tpl"}
</div>
{/if}


</form>