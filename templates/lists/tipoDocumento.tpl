<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
	{include file="{$DOC_ROOT}/templates/items/tipoDocumento-header.tpl"}
<tbody>
	{include file="{$DOC_ROOT}/templates/items/tipoDocumento-base.tpl"}
</tbody>
</table>
	{if count($resTipoDocumento.pages)}
	{include file="{$DOC_ROOT}/templates/lists/pages.tpl" pages=$resTipoDocumento.pages}
	{/if}
