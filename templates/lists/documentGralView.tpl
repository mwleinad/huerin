<table width="90%" cellpadding="0" cellspacing="0" border="0" style="border:1">
<tr>
	<td align="center" width="50"><b>APLICA</b></td>
	<td align="center"><b>NOMBRE</b></td>
    <td align="center" width="70"><b>FECHA DE VENCIMIENTO</b></td>
    <td align="center" width="10"></td>
    <td align="center" width="70"><b>FECHA DE CUMPLIMIENTO</b></td>
</tr>
{foreach from=$docsGral item=item key=key}
<tr>
	<td align="center">
    {if $item.aplica} Si {else} No {/if}
    </td>
    <td align="center">{$item.name}</td>
    <td align="left">
    <b>Fecha:</b>
    <br />
    {$item.fecha}
    {if $item.prorrogas}
        <div align="left">
        <b>Prorrogas:</b>
        {foreach from=$item.prorrogas item=itm key=ky}
            <br />{$itm.fecha}            
        {/foreach}
        </div>
    {/if}
    </td>
    <td align="center">&nbsp;</td>
    <td align="center" valign="top">{$item.fechaRec}</td>
</tr>
{foreachelse}
<tr><td align="center" colspan="5">Ningun documento encontrado.</td></tr>
{/foreach}
</table>