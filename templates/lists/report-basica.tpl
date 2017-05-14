<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
<thead>
	<tr>
		<th align="center" width="150">FOLIO</th>
        <th align="center" width="150">PROYECTO</th>
        <th align="center">DOCUMENTO</th>
        <th align="center" width="120">FECHA DE RECIBIDO</th>
	</tr>
</thead>
<tbody>

{if $docsRet}
<tr>
	<td colspan="4" align="center"><b>DOCUMENTACION SIN ENTREGAR</b></td>
</tr>   
{foreach from=$docsRet item=item key=key}
<tr class="st{$item.status}">
    <td align="center" class="txtSt{$item.status}">{$item.folio}</td>
    <td align="center" class="txtSt{$item.status}">{$item.proyecto}</td>
    <td align="center" class="txtSt{$item.status}">{$item.name}</td>
    <td align="center" class="txtSt{$item.status}">
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
<tr class="st{$item.status}">
    <td align="center" class="txtSt{$item.status}">{$item.folio}</td>
    <td align="center" class="txtSt{$item.status}">{$item.proyecto}</td>
    <td align="center" class="txtSt{$item.status}">{$item.name}</td>
    <td align="center" class="txtSt{$item.status}">
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