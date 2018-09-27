<div  style="overflow:scroll; width:100%; height:600px">
<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b" class="rport-doc-perm" style="font-size:10px;">
<thead>
	<tr >
		<th align="center" width="60">Cliente</th>
		<th align="center" width="60">Razon Social</th>
		{foreach from=$tiposDocumentos item=docto name=foreachDoctos}
			<th align="center" width="20" style="font-size:10px !important;">{$docto.nombre}</th>
		{/foreach}
	</tr>
</thead>
<tbody>
{foreach from=$contracts item=contract name=forCont}
	{if $smarty.foreach.forCont.iteration is odd}{assign var="isodd" value="ok"}{else}{assign var="isodd" value=""}{/if}
<tr class='{if $isodd == "ok"}rport-doc-perm-dark{/if}'>
    <td align="center" class="" rowspan="1" title="{$contract.nameContact}">{$contract.nameContact}&nbsp;</td>
    <td align="center" class="" rowspan="1" title="{$contract.name}">{$contract.name}&nbsp;</td>
	{foreach from=$contract.documentos item=documento}
				{if $documento.fileExist}
					{assign var="style" value="background:#00dd00;"}
					{assign var="styleBg" value="bgcolor='#00dd00'"}
				{else}
					{if $documento.typeRequired eq 'Obligatorio' && !$documento.fileExist}
						{assign var="style" value="background:#d94438;"}
						{assign var="styleBg" value="bgcolor='#d94438'"}
					{elseif $documento.typeRequired eq 'Opcional' || $documento.typeRequired eq 'Condicional'}
						{assign var="style" value="background:#f7ff45;"}
						{assign var="styleBg" value="bgcolor='#f7ff45'"}
					{/if}
				{/if}
    <td align="center" title="" style="width:20px !important; {$style} " {$styleBg}>&nbsp;
	</td>
	{assign var="style" value=""}
	{assign var="styleBg" value=""}
	{/foreach}
</tr>
{foreachelse}
<tr>
	<td align="center" colspan="20">Ning&uacute;n registro encontrado.</td>
</tr>
{/foreach}
</tbody>
</table>
</div>

