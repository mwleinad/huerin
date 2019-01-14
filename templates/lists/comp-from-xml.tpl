<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
	{include file="{$DOC_ROOT}/templates/items/comp-from-xml-header.tpl"}
<tbody>
   {foreach from=$facturas item=fact key=key}
	{include file="{$DOC_ROOT}/templates/items/comp-from-xml-base.tpl"}
   {/foreach}
</tbody>
</table>