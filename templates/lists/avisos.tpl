
<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
	{include file="{$DOC_ROOT}/templates/items/avisos-header.tpl"}
<tbody>
	{include file="{$DOC_ROOT}/templates/items/avisos-base.tpl"}
</tbody>
</table>

<br />
{if count($notices.pages)}
	{include file="{$DOC_ROOT}/templates/lists/pages.tpl" pages=$notices.pages}
{/if}