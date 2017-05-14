<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
	{include file="{$DOC_ROOT}/templates/items/tipoServicio-header.tpl"}
<tbody>
	{include file="{$DOC_ROOT}/templates/items/tipoServicio-base.tpl"}
</tbody>
</table>
	{if count($resTipoServicio.pages)}
	{include file="{$DOC_ROOT}/templates/lists/pages.tpl" pages=$resTipoServicio.pages}
	{/if}
