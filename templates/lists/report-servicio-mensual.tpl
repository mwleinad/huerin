<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b" style="font-size:10px">
<thead>
	<tr>
		<th align="center" width="60">Cliente</th>
		<th align="center" width="60">C. Asignado</th>
		<th align="center" width="60">Razon Social</th>
		<th align="center" width="60">Servicio</th>
		<th align="center" width="50">{$mes}</th>
		{for $foo=2 to $maxSteps}
		<th align="center" width="50"></th>
		{/for}
	</tr>
</thead>
<tbody>
{if $cleanedArray}
{foreach from=$cleanedArray item=item key=key}

		<tr>
    		<td align="center" class="" title="{$item.nameContact}">{$item.nameContact}</td>
    		<td align="center" class="" title="{$item.responsable}">{$item.responsable}</td>
    		<td align="center" class="" title="{$item.name}">{$item.name}</td>
    	<td align="center" class="" title="{$contract.responsable.name}">{$item.nombreServicio}</td>
    	{foreach from=$item.instanciasServicio item=instanciaServicio}

    		{for $foo=0 to $maxSteps-1}
				{if $instanciaServicio.steps.$foo.nombreStep!=""}
				<td  align="center"
					 class="{if $instanciaServicio.steps.$foo.class eq 'CompletoTardio'}
      							st{'Completo'} txtSt{'Completo'}
      						{else}
      							{if $instanciaServicio.steps.$foo.class eq 'Iniciado'}
      								st{'PorCompletar'} txtSt{'PorCompletar'}
      							{else}
      								st{$instanciaServicio.steps.$foo.class} txtSt{$instanciaServicio.steps.$foo.class}
      							{/if}
      						{/if}"
					 title="{$servicio.nombreServicio}{if $instanciaServicio.steps.$foo.class eq 'CompletoTardio'}{'Completo'}{else}{if $instanciaServicio.steps.$foo.class eq 'Iniciado'}{'PorCompletar'}{else}{$instanciaServicio.steps.$foo.class}{/if}{/if}">	
		<div style="cursor:pointer" {if in_array(108,$permissions)||$User.isRoot}onclick="GoToWorkflow('report-servicios', '{$instanciaServicio.instanciaServicioId}')"{/if}>Paso {$foo+1} <br> {$instanciaServicio.steps.$foo.nombreStep}</div>
				</td>
				{else}
				<td  colspan="3"></td>
				{/if}
    		{/for}
    	{/foreach}
		</tr>
{/foreach}
{/if}

</tbody>
</table>