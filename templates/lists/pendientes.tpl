
<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
	{include file="{$DOC_ROOT}/templates/items/pendientes-header.tpl"}
<tbody>
	{include file="{$DOC_ROOT}/templates/items/pendientes-base.tpl"}
</tbody>
</table>

<br />
{if count($pendientes.pages)}
	{include file="{$DOC_ROOT}/templates/lists/pages.tpl" pages=$pendientes.pages}
{/if}