
<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
	{include file="{$DOC_ROOT}/templates/items/avisos-header.tpl"}
<tbody>
	{include file="{$DOC_ROOT}/templates/items/avisos-base.tpl"}
</tbody>
</table>
<div class="pagination" style="text-align: right">
    {if count($notices.pages)}
        {include file="{$DOC_ROOT}/templates/lists/pages_new.tpl" pages=$notices.pages}
    {/if}
</div>
