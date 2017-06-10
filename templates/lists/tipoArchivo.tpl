<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
	{include file="{$DOC_ROOT}/templates/items/tipoArchivo-header.tpl"}
<tbody>
	{include file="{$DOC_ROOT}/templates/items/tipoArchivo-base.tpl"}
</tbody>
</table>
	{if count($resTipoArchivo.pages)}
	{include file="{$DOC_ROOT}/templates/lists/pages.tpl" pages=$resTipoArchivo.pages}
	{/if}
