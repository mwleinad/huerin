<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
	{include file="{$DOC_ROOT}/templates/items/tipoRequerimiento-header.tpl"}
<tbody>
	{include file="{$DOC_ROOT}/templates/items/tipoRequerimiento-base.tpl"}
</tbody>
</table>
	{if count($resTipoDocumento.pages)}
	{include file="{$DOC_ROOT}/templates/lists/pages.tpl" pages=$resTipoRequerimiento.pages}
	{/if}
