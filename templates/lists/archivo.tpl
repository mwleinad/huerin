<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
	{include file="{$DOC_ROOT}/templates/items/archivo-header.tpl"}
<tbody>
	{include file="{$DOC_ROOT}/templates/items/archivo-base.tpl"}
</tbody>
</table>
	{if count($resArchivo.pages)}
	{include file="{$DOC_ROOT}/templates/lists/pages.tpl" pages=$resArchivo.pages}
	{/if}
