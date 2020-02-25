<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a" summary="Employee Pay Sheet">
{include file="{$DOC_ROOT}/templates/items/folio_header.tpl" clase="Off"}
{if count($folios)}
	<tbody>
	{foreach from=$folios item=folio key=key}
		{if $key%2 == 0}
			{include file="{$DOC_ROOT}/templates/items/folio_base.tpl" clase="Off"}
		{else}
			{include file="{$DOC_ROOT}/templates/items/folio_base.tpl" clase="On"}
		{/if}
	{/foreach}
	</tbody>
</table>
  {include file="{$DOC_ROOT}/templates/lists/pages_new.tpl" pages=$pages}
{else}
<tr><td colspan="7"><div align="center">No existen folios en estos momentos.</div></td></tr>
</table>
{/if}