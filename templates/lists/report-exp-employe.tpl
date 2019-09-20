{assign var=cols value=$results.expedientes|count}
<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b" style="font-size:10px">
<thead>
	<tr>
		<th align="center" style="width: 20%">Nombre</th>
		{foreach from=$results.expedientes item=item}
			<th align="center">{$item.name}</th>
		{/foreach}
	</tr>
</thead>
		{foreach from=$results.employes item=employe key=key}
		<tr>
    		<td align="center" style="width: 20%" title="{$employe.nameContact}">{$employe.name}</td>
			{foreach from=$employe.ownExpedientes item=expediente name=ownExpedientes}
				<td align="center"
				    style="background-color:{$expediente.background} !important;color:#FFF">{$expediente.fecha}
				</td>
			{foreachelse}
				<td  colspan="{$cols}" align="center"
					style="background-color:#EFEFEF !important;color:#000000">Expedientes no configurado
				</td>
			{/foreach}
		</tr>
		{foreachelse}
		<tr>
			<td colspan="{$cols+1}" align="center">Ning&uacute;n registro encontrado.</td>
		</tr>
		{/foreach}
</tbody>
</table>