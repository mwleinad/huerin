<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b" class="rport-doc-perm" style="font-size:14px;">
    {assign var="totalColspan" value={$tiposDocumentos|@count}}
<thead>
     <tr aria-rowspan="10">
        <th colspan="{$totalColspan+3}" class="divInside">
          {$personal}<br>
		   Se presenta la lista de razones sociales con documentos pendientes por subir, favor de atender los pendientes.
		</th>
	</tr>
    <tr>
		<th class="grayBox" colspan="{($totalColspan+2)/5}" align="right">
			Identificador de colores =>
		</th>
		<th class="greenBox" colspan="{($totalColspan+2)/5}">
			Archivo requerido subido correctamente
		</th>
		<th class="redBox" colspan="{($totalColspan+2)/5}">
			Archivo requerido pendiente por subir
		</th>
		<th class="grayBox" colspan="{($totalColspan+2)/5}">
			Este archivo no es requerido para esta razon social
		</th>
	</tr>
	<tr >
		<th class="cabeceraTabla" align="center" width="60">Cliente</th>
		<th class="cabeceraTabla" align="center" width="60">Razon Social</th>
		<th class="cabeceraTabla" align="center" width="60">Responsable</th>
		{foreach from=$tiposDocumentos item=docto name=foreachDoctos}
		<th class="cabeceraTabla" align="center" width="20" style="font-size:10px !important;">{$docto.nombre}</th>
			<!--{if $smarty.foreach.foreachDoctos.iteration == $totalDocumentos}
			</tr>
			<tr>
			{/if}-->
		{/foreach}

	</tr>
</thead>
<tbody>
{foreach from=$contracts item=contract name=forCont}

<tr {if $smarty.foreach.forCont.iteration is odd}bgcolor="#dddddd"{/if}>
    <td align="center">{$contract.nameContact}&nbsp;</td>
    <td align="center">{$contract.name}&nbsp;</td>
	<td align="center">{$contract.responsableArea}&nbsp;</td>
	{foreach from=$contract.documentos item=docto key=kdoc}
    <td align="center" class="{if $docto.fileExist&&$docto.required}st{'Completo'}{elseif !$docto.fileExist&&$docto.required}st{'PorIniciar'}{/if}">&nbsp;
	</td>
	{/foreach}
</tr>
{foreachelse}
<tr>
	<td align="center" colspan="{$totalColspan+3}">Ning&uacute;n registro encontrado.</td>
</tr>
{/foreach}
</tbody>
</table>

