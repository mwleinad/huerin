{foreach from=$subcategories item=item key=key}
        <tr id="1">
        <td align="center" class="id">{$item.contSubcatId}</td>        
        <td align="center">{$item.name}</td>
        <td align="center">
        {if $item.active}
        	Si
        {else}
        	No
        {/if}    
        </td>       
        <td align="center">            
            <img src="{$WEB_ROOT}/images/icons/action_delete.gif" class="spanDelete" id="{$item.contSubcatId}" title="Eliminar"/>
            <img src="{$WEB_ROOT}/images/icons/edit.gif" class="spanEdit" id="{$item.contSubcatId}" title="Editar"/>
        </td>
    </tr>
{foreachelse}
<tr><td align="center" colspan="4">No se encontr&oacute; ning&uacute;n registro.</td></tr>			
{/foreach}