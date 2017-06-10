{foreach from=$notices.items item=item key=key}
    {if $item.priority == "importante"}
    	{assign var=cFont value="#FF0000"}
    {else}
    	{assign var=cFont value="#000000"}
       {/if}
	<tr id="1">
         <td align="center"  width="20%"><font color="{$cFont}"}>{$item.usuario}</font></td>
         <td align="center" width="10%"><font  color="{$cFont}"}>{$item.fecha}</font></td>
         <td align="center" width="60%"><font  color="{$cFont}"}>{$item.description}</font></td>
         <td align="justify" width="10%">
         {if $item.url!=""}
         <a href="{$WEB_ROOT}/archivos/{$item.url}" target="_blank">
			   <img src="{$WEB_ROOT}/images/icons/down.png"  border="0" title="Descargar archivo" />
              
         </a> 
         {/if}  
         {if $User.tipoPersonal == "Socio" || $User.tipoPersonal == "Asistente"}
         <a href="javascript:;" onclick="DeleteNotice({$item.noticeId})">
	   		<img src="{$WEB_ROOT}/images/icons/action_delete.gif" border="0"/>               
         </a> 
         {/if}
         </td>
       
	</tr>
{foreachelse}
	<tr><td align="center" colspan="4">No se encontr&oacute; ning&uacute;n registro.</td></tr>
{/foreach}