<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
	{include file="{$DOC_ROOT}/templates/items/documento-header.tpl"}
<tbody>
	{include file="{$DOC_ROOT}/templates/items/documento-base.tpl"}
</tbody>
</table>
	{if count($resDocumento.pages)}
	{include file="{$DOC_ROOT}/templates/lists/pages.tpl" pages=$resDocumento.pages}
	{/if}
