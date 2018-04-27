<table width="100%" cellpadding="0" cellspacing="0" id="boxTable">
<thead>
	<tr>
		<th align="center" colspan="2" style="border-top:1px solid #CCC">        
        <div style="float:left; margin-left:400px">DOCUMENTOS</div>
        <div class="iconSH" id="tbInfSubS" onclick="toggleSection('tbInfSub',1)" style="display:none">[+]</div>
        <div class="iconSH" id="tbInfSubH" onclick="toggleSection('tbInfSub',0)">[-]</div>
        </th>
	</tr>
</thead>
<tbody id="tbInfSub">
  <tr>
		<td align="left" width="40%" class="tdPad">
        {if (in_array(72,$permissions)&&in_array(73,$permissions))||$User.isRoot}
    	<a href="javascript:;" id="{$contractInfo.contractId}" class="addDocumento"><img src="{$WEB_ROOT}/images/icons/add.png" title="Agregar Documento"/> Agregar Documento</a>
		{/if}
		<div id="contentDocumentos">
			{include file="{$DOC_ROOT}/templates/lists/documento.tpl"}    
        </div>
      </td>
	</tr>
</tbody>
</table>