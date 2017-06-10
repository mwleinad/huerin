<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
	{include file="{$DOC_ROOT}/templates/items/requerimiento-header.tpl"}
<tbody>
	{include file="{$DOC_ROOT}/templates/items/requerimiento-base.tpl"}
</tbody>
</table>
	{if count($resDocumento.pages)}
	{include file="{$DOC_ROOT}/templates/lists/pages.tpl" pages=$resRequerimiento.pages}
	{/if}
