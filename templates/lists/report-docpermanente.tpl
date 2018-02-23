<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b" class="rport-doc-perm" style="font-size:14px;">
    {assign var="totalColspan" value={$tiposDocumentos|@count}}
<thead>
     <tr aria-rowspan="10">
        <th colspan="{$totalColspan+4}" class="divInside" align="center">
          {$personal}<br>
		   Se presenta la lista de razones sociales con documentos pendientes por subir en el area de {$depto|ucfirst}, favor de atender los pendientes.
		</th>
	</tr>
    <tr>
		<th class="grayBox" colspan="{($totalColspan+4)/5}" align="right">
			Guia de colores
		</th>
		<th class="greenBox" colspan="{($totalColspan+4)/5}" align="center">
			Celda verde indica que la razon social(cliente) cumple con el documento.
		</th>
		<th class="redBox" colspan="{($totalColspan+4)/5}" align="center">
			Celda roja indica que el documento es obligatorio para el area de {$depto|ucfirst} y ademas falta por subir a la plataforma.
		</th>
		<th class="grayBox" colspan="{($totalColspan+4)/5 +($totalColspan+4)%5}" align="center">
			Celdas sin color indican que el documento no pertenece a su area, es opcional o bien no aplica para el tipo de persona al cual pertenece
			la razon social(cliente).
		</th>
	</tr>
	<tr >
		<th class="cabeceraTabla" align="center" width="60">Cliente</th>
		<th class="cabeceraTabla" align="center" width="60">Razon Social</th>
		<th class="cabeceraTabla" align="center" width="60">Persona</th>
		<th class="cabeceraTabla" align="center" width="60">Responsable</th>
		{foreach from=$tiposDocumentos item=docto name=foreachDoctos}
		<th class="cabeceraTabla" align="center" width="20" style="font-size:10px !important;">{$docto.nombre}</th>
		{/foreach}

	</tr>
</thead>
<tbody>
{foreach from=$contracts item=contract name=forCont}

<tr {if $smarty.foreach.forCont.iteration is odd}bgcolor="#dddddd"{/if}>
    <td align="center">{$contract.nameContact}&nbsp;</td>
    <td align="center">{$contract.name}&nbsp;</td>
	<td align="center">{$contract.type}&nbsp;</td>
	<td align="center">{$contract.responsableArea}&nbsp;</td>
	{foreach from=$contract.documentos item=docto key=kdoc}
    <td align="center" class="{if $docto.fileExist&&$docto.required}st{'Completo'}{elseif !$docto.fileExist&&$docto.required}st{'PorIniciar'}{/if}">&nbsp;
	</td>
	{/foreach}
</tr>
{foreachelse}
<tr>
	<td align="center" colspan="{$totalColspan+4}">Ning&uacute;n registro encontrado.</td>
</tr>
{/foreach}
</tbody>
</table>

