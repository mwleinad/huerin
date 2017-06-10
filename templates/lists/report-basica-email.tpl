<div style="font-size:10px;font-family:Arial">

<table width="550" cellpadding="0" cellspacing="0" border="0">
<thead>
    <tr bgcolor="#E2E2E2">
        <th align="center" width="80">FOLIO</th>
        <th align="center" width="150">PROYECTO</th>
        <th align="center">DOCUMENTO</th>
        <th align="center" width="60">FECHA DE RECIBIDO</th>
    </tr>
</thead>
<tbody>

{if $docsRet}
<tr>
	<td colspan="4" align="center"><b>DOCUMENTACION SIN ENTREGAR</b></td>
</tr>   
{foreach from=$docsRet item=item key=key}
{if $item.status == "Entregado"}
<tr bgcolor="#009900" style="color:#FFF" height="30">
{elseif $item.status == "Retrasado"}
<tr bgcolor="#F00000" style="color:#FFF" height="30">
{/if}
    <td align="center" height="15">{$item.folio}</td>
    <td align="left">{$item.proyecto}</td>
    <td align="left">{$item.name}</td>
    <td align="center">
        {if $item.fechaRec == ""}
    	PENDIENTE
        {else}
            {$item.fechaRec}
        {/if}
    </td>
</tr>
{/foreach}
{/if}

{if $docsEnt}
<tr>
	<td colspan="4" align="center"><b>DOCUMENTACION ENTREGADA</b></td>
</tr>   
{foreach from=$docsEnt item=item key=key}
{if $item.status == "Entregado"}
<tr bgcolor="#009900" style="color:#FFF" height="30">
{elseif $item.status == "Retrasado"}
<tr bgcolor="#F00000" style="color:#FFF" height="30">
{/if}
    <td align="center" height="15">{$item.folio}</td>
    <td align="left">{$item.proyecto}</td>
    <td align="left">{$item.name}</td>
    <td align="center">
        {if $item.fechaRec == ""}
    	PENDIENTE
        {else}
            {$item.fechaRec}
        {/if}
    </td>
</tr>
{/foreach}
{/if}

</tbody>
</table>

</div>