<div style="font-size:10px;font-family:Arial">

<table width="550" cellpadding="0" cellspacing="0" border="0">
<thead>
    <tr bgcolor="#E2E2E2">
        <th align="center" width="80">FOLIO</th>
        <th align="center" width="150">PROYECTO</th>
        <th align="center">DOCUMENTO</th>
        <th align="center" width="60">FECHA DE VENCIMIENTO</th>
        <th align="center" width="60">FECHA DE CUMPLIMIENTO</th>
    </tr>
</thead>
<tbody>

{if $docsRet}
<tr>
	<td colspan="5" align="center"><b>OBLIGACIONES VENCIDAS</b></td>
</tr>  
{foreach from=$docsRet item=item key=key}
{if $item.status == "Futuro"}
<tr bgcolor="#003399" style="color:#FFF" height="30">
{elseif $item.status == "Proximo"}
<tr bgcolor="#FFFF00" style="color:#000" height="30">
{elseif $item.status == "Entregado"}
<tr bgcolor="#009900" style="color:#FFF" height="30">
{elseif $item.status == "Retrasado"}
<tr bgcolor="#F00000" style="color:#FFF" height="30">
{/if}
    <td align="center" height="15">{$item.folio}</td>
    <td align="left">{$item.proyecto}</td>
    <td align="left">{$item.name}</td>
    <td align="center">
    {if $item.fechaEnt == ""}
    	PENDIENTE
    {else}
    	{$item.fechaEnt}
    {/if}
    </td>
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

{if $docsProx}
<tr>
	<td colspan="5" align="center"><b>OBLIGACIONES PROXIMAS A CUMPLIR EN LOS SIGUIENTES 15 DIAS</b></td>
</tr>  
{foreach from=$docsProx item=item key=key}
{if $item.status == "Futuro"}
<tr bgcolor="#003399" style="color:#FFF" height="30">
{elseif $item.status == "Proximo"}
<tr bgcolor="#FFFF00" style="color:#000" height="30">
{elseif $item.status == "Entregado"}
<tr bgcolor="#009900" style="color:#FFF" height="30">
{elseif $item.status == "Retrasado"}
<tr bgcolor="#F00000" style="color:#FFF" height="30">
{/if}
    <td align="center" height="15">{$item.folio}</td>
    <td align="left">{$item.proyecto}</td>
    <td align="left">{$item.name}</td>
    <td align="center">
    {if $item.fechaEnt == ""}
    	PENDIENTE
    {else}
    	{$item.fechaEnt}
    {/if}
    </td>
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

{if $docsFut}
<tr>
	<td colspan="5" align="center"><b>OBLIGACIONES POR CUMPLIR</b></td>
</tr>  
{foreach from=$docsFut item=item key=key}
{if $item.status == "Futuro"}
<tr bgcolor="#003399" style="color:#FFF" height="30">
{elseif $item.status == "Proximo"}
<tr bgcolor="#FFFF00" style="color:#000" height="30">
{elseif $item.status == "Entregado"}
<tr bgcolor="#009900" style="color:#FFF" height="30">
{elseif $item.status == "Retrasado"}
<tr bgcolor="#F00000" style="color:#FFF" height="30">
{/if}
    <td align="center" height="15">{$item.folio}</td>
    <td align="left">{$item.proyecto}</td>
    <td align="left">{$item.name}</td>
    <td align="center">
    {if $item.fechaEnt == ""}
    	PENDIENTE
    {else}
    	{$item.fechaEnt}
    {/if}
    </td>
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
	<td colspan="5" align="center"><b>OBLIGACIONES CUMPLIDAS</b></td>
</tr>  
{foreach from=$docsEnt item=item key=key}
{if $item.status == "Futuro"}
<tr bgcolor="#003399" style="color:#FFF" height="30">
{elseif $item.status == "Proximo"}
<tr bgcolor="#FFFF00" style="color:#000" height="30">
{elseif $item.status == "Entregado"}
<tr bgcolor="#009900" style="color:#FFF" height="30">
{elseif $item.status == "Retrasado"}
<tr bgcolor="#F00000" style="color:#FFF" height="30">
{/if}
    <td align="center" height="15">{$item.folio}</td>
    <td align="left">{$item.proyecto}</td>
    <td align="left">{$item.name}</td>
    <td align="center">
    {if $item.fechaEnt == ""}
    	PENDIENTE
    {else}
    	{$item.fechaEnt}
    {/if}
    </td>
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

</body>