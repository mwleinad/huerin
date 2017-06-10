<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border:0">
<tr>
	<td align="center"><b>NOMBRE</b></td>
    <td align="center">&nbsp;</td>
    <td align="center" width="80"><b>FECHA DE RECIBIDO DE ROQUE&Ntilde;I</b></td>
    <td align="center" width="10"></td>
    <td align="center" width="80"><b>FECHA DE ENVIO A WALMART</b></td>
    <td align="center" width="10"></td>
</tr>
{foreach from=$docsSellado item=item key=key}
<tr>
    <td align="left">{$item.name}</td>
    <td align="center">&nbsp;</td>
    <td align="center">{$item.fechaRec}</td>
    <td align="left"></td>
    <td align="center">{$item.fecha}</td>
    <td></td>
    <td align="center">
    {if $item.archivo}
    <a href="{$WEB_ROOT}/archivos/{$item.archivo}" target="_blank">
    <img src="{$WEB_ROOT}/images/icons/file.png" border="0" />
    </a>
    {/if}
    </td>    
</tr>
{foreachelse}
<tr><td align="center" colspan="6">Ningun documento encontrado.</td></tr>
{/foreach}
</table>