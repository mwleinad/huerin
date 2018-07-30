<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
	{include file="{$DOC_ROOT}/templates/items/expediente-header.tpl"}
<tbody>
	{include file="{$DOC_ROOT}/templates/items/expediente-base.tpl"}
</tbody>
</table>
	{if count($expedientes.pages)}
	{include file="{$DOC_ROOT}/templates/lists/pages.tpl" pages=$expedientes.pages}
	{/if}
