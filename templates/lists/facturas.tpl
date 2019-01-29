{include file="{$DOC_ROOT}/templates/items/factura_header.tpl" clase="Off"}
{if count($comprobantes.items)}
	{foreach from=$comprobantes.items item=fact key=key}
    	{if $key%2 == 0}
			{include file="{$DOC_ROOT}/templates/items/factura_base.tpl" clase="Off"}
        {else}
			{include file="{$DOC_ROOT}/templates/items/factura_base.tpl" clase="On"}
        {/if}
	{/foreach}
 	<tr class="footer">
                <td colspan="10" align="right">
				<!--  PAGINATION START  -->
	<div class="pagination">
	{if count($comprobantes.pages)}
        {include file="{$DOC_ROOT}/templates/lists/pages_new.tpl" pages=$comprobantes.pages}
    {/if}
    </div>
                <!--  PAGINATION END  -->       
                </td>
              </tr>
			</tbody>
          </table>
        </form>
		</div>
      </div>
<!--  END #PORTLETS -->  
   </div>
{else}
<div align="center">No existen facturas en estos momentos.</div>
{/if}