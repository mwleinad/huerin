{include file="{$DOC_ROOT}/templates/items/cxc_header.tpl" clase="Off"}
{if count($comprobantes.items)}
	{foreach from=$comprobantes.items item=fact key=key}
    	{if $key%2 == 0}
			{include file="{$DOC_ROOT}/templates/items/cxc_base.tpl" clase="Off"}
        {else}
			{include file="{$DOC_ROOT}/templates/items/cxc_base.tpl" clase="On"}
        {/if}
	{/foreach}

  <tr>
      <td align="center"></td>
      <td width="34"></td>
      <td align="center"></td>
      <td align="center"></td>
      <td  align="right"><b>${$total|number_format:2}</b></td>
      <td align="right"><b>${$payments|number_format:2}</b></td>
      <td  align="right"><b>${$saldo|number_format:2}</b></td>
      <td width="90"></td>
    </tr>
               
 	{if count($comprobantes.pages)}
    {include file="{$DOC_ROOT}/templates/lists/pages_new.tpl" pages=$comprobantes.pages}
  {/if}
  
{else}
<div align="center">Sin Resultados. Favor de hacer una busqueda primero.</div>
{/if}
</tbody>
			 </table> 
			 </form>

</div></div></div>