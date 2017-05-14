<div  style="overflow:scroll; width:100%; height:600px">
<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b" class="rport-doc-perm" style="font-size:10px;">
<thead>
	<tr>
		<th align="center" width="60" >Cliente</th>
		<th align="center" width="60" >Razon Social</th>
		{foreach from=$tiposArchivos item=arc name=foreachArcs}
    
    {if $arc.descripcion != "CODIGO BARRA BIDIMENSIONAL"}
		<th align="center" width="20" style="font-size:10px !important;">{$arc.descripcion}</th>
    {/if}
			{if $smarty.foreach.foreachArcs.iteration == $totalArchivos}
		<th align="center" width="20" style="font-size:10px !important;">Clave FIEL</th>
		<th align="center" width="20" style="font-size:10px !important;">Clave CIEC</th>
			
			{/if}
		{/foreach}

		<th align="center" width="20" style="font-size:10px !important;">Clave IDSE</th>
		<th align="center" width="20" style="font-size:10px !important;">Clave Impuesto Sobre Nomina</th>
	</tr>
</thead>
<tbody>
{foreach from=$contracts item=contract name=forCont}
	{if $smarty.foreach.forCont.iteration is odd}{assign var="isodd" value="ok"}{else}{assign var="isodd" value=""}{/if}
<tr class='{if $isodd == "ok"}rport-doc-perm-dark{/if}'>
    <td align="center" class=""  title="{$contract.nameContact}">{$contract.nameContact}&nbsp;</td>
    <td align="center" class=""  title="{$contract.name}">{$contract.name}&nbsp;</td>
	{foreach from=$tiposArchivos item=arc name=foreachTiposArcs}
  
      {if $arc.descripcion != "CODIGO BARRA BIDIMENSIONAL"}

			{foreach from=$contract.archivos item=archivo}
				{if $arc.tipoArchivoId == $archivo.tipoArchivoId}
					{assign var="style" value=$archivo.dateColor}
					{assign var="value" value=$archivo.date}
				{/if}
			{/foreach}

    <td align="center" title="" style="width:20px !important; background-color:{$style}; color:#FFFFFF;" {if $style != ""}bgcolor="{$style}"{/if}>
    		{/if}
			&nbsp;{$value}
	</td>
	{assign var="style" value=""}
	{assign var="value" value=""}
 
			{if $smarty.foreach.foreachTiposArcs.iteration == $totalArchivos}
				<td align="center" title="" style="{if $contract.claveFiel != ''}background-color:#00FF00; color:#FFFFFF;{/if}"  {if $contract.claveFiel != ""}bgcolor="00FF00"{/if}>{$contract.claveFiel}</td>
	<td align="center" title="" style="{if $contract.claveCiec != ''}background-color:#00FF00; color:#FFFFFF;{/if}" {if $contract.claveCiec != ""}bgcolor="00FF00"{/if}>{$contract.claveCiec}</td>
			<!--</tr>
			<tr class='{if $isodd == "ok"}rport-doc-perm-dark{/if}'>-->
			{/if}

	{/foreach}
	<td align="center" title="" style="{if $contract.claveIdse != ''}background-color:#00FF00;{/if}">{$contract.claveIdse}</td>
	<td align="center" title="" style="{if $contract.claveIsn != ''}background-color:#00FF00;{/if}">{$contract.claveIsn}</td>
</tr>
{foreachelse}
<tr>
	<td colspan="20" align="center">Ning&uacute;n registro encontrado.</td>
</tr>
{/foreach}
</tbody>
</table>
</div>

