<form name="frmContract" id="frmContract" enctype="multipart/form-data" method="post" autocomplete="off">
<input type="hidden" name="action" value="save" />
{include file="{$DOC_ROOT}/templates/forms/add-contract-basic.tpl"}
<div id="infoCompraVenta2" style="display:block">
	{include file="{$DOC_ROOT}/templates/forms/datos-contacto.tpl"}
</div>

<div id="infoCompraVenta3" style="display:block">
	{include file="{$DOC_ROOT}/templates/forms/fiel.tpl"}
</div>


<div id="infoCompraVenta" style="display:block">
	{*include file="{$DOC_ROOT}/templates/forms/documentos.tpl"*}
</div>

<div id="infoArrendamiento2" style="display:block">
	{*include file="{$DOC_ROOT}/templates/forms/archivos.tpl"*}
</div>

<div style="text-align: center">
    <div>¿ Enviar notificacion por correo ? </div>
    <input type="checkbox" name="sendNotificacion" id="sendNotificacion">
</div>

</form>