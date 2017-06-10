{include file="{$DOC_ROOT}/templates/items/cfdiPagos_header.tpl" clase="Off"}
{if count($comprobantes.items)}
	{foreach from=$comprobantes.items item=fact key=key}
    	{if $key%2 == 0}
			{include file="{$DOC_ROOT}/templates/items/cfdiPagos_base.tpl" clase="Off"}
        {else}
			{include file="{$DOC_ROOT}/templates/items/cfdiPagos_base.tpl" clase="On"}
        {/if}
	{/foreach}

 	{if count($comprobantes.pages)}
    {include file="{$DOC_ROOT}/templates/lists/pages_new.tpl" pages=$comprobantes.pages}
  {/if}
  
{else}
<div align="center">No existen facturas en estos momentos.</div>
{/if}
</tbody>
			 </table> 
			 </form>

</div></div></div>