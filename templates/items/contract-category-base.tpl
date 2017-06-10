{foreach from=$categories item=item key=key}
        <tr id="1">
        <td align="center" class="id">{$item.contCatId}</td>
        <td align="center">{$item.name}</td>
        <td align="center">
        {if $item.active}
        	Si
        {else}
        	No
        {/if}    
        </td>
        <td align="center">
        <a href="{$WEB_ROOT}/contract-subcategory/contCatId/{$item.contCatId}">
        	<img src="{$WEB_ROOT}/images/icons/add.png" border="0" width="16" height="16" title="Agregar Subcategorias" />
        </a>
        </td>
        <td align="center">
        {*     	         
            <img src="{$WEB_ROOT}/images/icons/action_delete.gif" class="spanDelete" id="{$item.contCatId}" title="Eliminar"/>
        *}
            <img src="{$WEB_ROOT}/images/icons/edit.gif" class="spanEdit" id="{$item.contCatId}" title="Editar"/>
        </td>
    </tr>
{foreachelse}
<tr><td align="center" colspan="5">No se encontr&oacute; ning&uacute;n registro.</td></tr>			
{/foreach}
