<table width="100%" cellpadding="0" cellspacing="0" id="boxTable">
<thead>
	<tr>
		<th align="center" colspan="2" style="border-top:1px solid #CCC">        
        <div style="float:left; margin-left:400px">ARCHIVOS</div>
        <div class="iconSH" id="tbInfSubArchS" onclick="toggleSection('tbInfSubArch',1)" style="display:none">[+]</div>
        <div class="iconSH" id="tbInfSubArchH" onclick="toggleSection('tbInfSubArch',0)">[-]</div>
        </th>
	</tr>
</thead>
<tbody id="tbInfSubArch">

  <tr>
		<td align="left" width="40%" class="tdPad">
    {if $canEdit || $User["userId"] == 149}
    	<a href="{$WEB_ROOT}/add-archivo/id/{$contractInfo.contractId}" onclick="return parent.GB_show('Agregar Archivo', this.href,200,970) "><img src="{$WEB_ROOT}/images/icons/add.png" title="Agregar Documento"/> Agregar Archivos</a>
		{/if}
			<div id="contentArchivos">
			{include file="{$DOC_ROOT}/templates/lists/archivo.tpl"}    
      </div>
      </td>
	</tr>

  

</tbody>
</table>