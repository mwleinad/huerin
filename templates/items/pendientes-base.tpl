{foreach from=$pendientes.items item=item key=key}
    {if $item.priority == "importante"}
    	{assign var=cFont value="#FF0000"}
    {else}
    	{assign var=cFont value="#000000"}
       {/if}
	<tr id="1">
         <td align="center"  width="20%"><font color="{$cFont}"}>{$item.usuario}</font></td>
         <td align="center"  width="20%"><font color="{$cFont}"}>{$item.name}</font></td>
         <td align="center" width="10%"><font  color="{$cFont}"}>{$item.fecha}</font></td>
         <td align="center" width="60%"><font  color="{$cFont}"}>{$item.description}</font></td>
         <td align="center" width="60%"><font  color="{$cFont}"}>{$item.status}</font></td>
         <td align="justify" width="10%">
         {if $item.status != "cerrado"}
         <a href="javascript:;" onclick="ClosePendiente({$item.pendienteId})">
	   		<img src="{$WEB_ROOT}/images/icons/action_delete.gif" border="0"/>               
         </a> 
         {/if}
         <a href="javascript:;" onclick="HistorialPopup({$item.pendienteId})">
	   		<img src="{$WEB_ROOT}/images/icons/calendar.png" border="0"/>               
         </a> 
         </td>
       
	</tr>
{foreachelse}
	<tr><td align="center" colspan="4">No se encontr&oacute; ning&uacute;n registro.</td></tr>
{/foreach}