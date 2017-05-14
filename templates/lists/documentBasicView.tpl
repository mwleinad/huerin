<table width="90%" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td align="center" width="50">APLICA</td>
    <td align="center">NOMBRE</td>
    <td align="center" width="120">FECHA RECIBIDO <br />ROQUE&Ntilde;I STRAFFON</td>
    <td align="center" width="250">DESC.</td>
</tr>
{foreach from=$docsBasic item=item key=key}
<tr>
    <td align="center">
    {if $item.aplica} Si {else} No {/if}
    </td>
    <td align="center" valign="top">{$item.name}</td>
    <td align="center">
    {$item.fechaRec}
    {foreach from = $item.docs item=itm key=ky}
        {if $ky != 0}
        <br />
        {$itm.fecha}
        {/if}
    {/foreach}    
    </td>
    <td align="left">
    {if $item.info}<b>{$item.info}:</b>{/if} {$item.descripcion} 
    <a href="{$WEB_ROOT}/archivos/{$item.docs.0.archivo}" target="_blank">{$item.docs.0.archivo}</a>
    
    {foreach from = $item.docs item=itm key=ky}
        {if $ky != 0}
        <br />
        <b>{$item.info}:</b> {$itm.description} <a href="{$WEB_ROOT}/archivos/{$itm.archivo}" target="_blank">{$itm.archivo}</a>
        {/if}
    {/foreach}    
    </td>
</tr>
{foreachelse}
<tr><td align="center" colspan="7">Ningun documento encontrado.</td></tr>
{/foreach}
</table>