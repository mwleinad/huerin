<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
	{include file="{$DOC_ROOT}/templates/items/departamentos-header.tpl"}
<tbody>
	{include file="{$DOC_ROOT}/templates/items/departamentos-base.tpl"}
</tbody>
</table>
	{if count($resDepartamentos.pages)}
	{include file="{$DOC_ROOT}/templates/lists/pages.tpl" pages=$resDepartamentos.pages}
	{/if}
