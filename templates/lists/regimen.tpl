<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
	{include file="{$DOC_ROOT}/templates/items/regimen-header.tpl"}
<tbody>
	{include file="{$DOC_ROOT}/templates/items/regimen-base.tpl"}
</tbody>
</table>
	{if count($resRegimen.pages)}
	{include file="{$DOC_ROOT}/templates/lists/pages.tpl" pages=$resRegimen.pages}
	{/if}
