<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">	{include file="{$DOC_ROOT}/templates/items/sociedad-header.tpl"}
<tbody>
	{include file="{$DOC_ROOT}/templates/items/sociedad-base.tpl"}
</tbody>
</table>
	{if count($resSociedad.pages)}
	{include file="{$DOC_ROOT}/templates/lists/pages.tpl" pages=$resSociedad.pages}
	{/if}
