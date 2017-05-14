<table width="100%" cellpadding="0" cellspacing="0" id="boxTable">
<thead>
	<tr>
		<th align="center" colspan="2" style="border-top:1px solid #CCC">        
        <div style="float:left; margin-left:400px">IMPUESTOS</div>
        <div class="iconSH" id="tbInfSubImpS" onclick="toggleSection('tbInfSubImp',1)" style="display:none">[+]</div>
        <div class="iconSH" id="tbInfSubImpH" onclick="toggleSection('tbInfSubImp',0)">[-]</div>
        </th>
	</tr>
</thead>
<tbody id="tbInfSubImp">

  <tr>
		<td align="left" width="40%" class="tdPad">
    	<a href="{$WEB_ROOT}/add-impuesto/id/{$contractInfo.contractId}" onclick="return parent.GB_show('Agregar Impuesto', this.href,200,970) "><img src="{$WEB_ROOT}/images/icons/add.png" title="Agregar Impuesto"/> Agregar Impuesto</a>

			<div id="contentImpuestos">
			{include file="{$DOC_ROOT}/templates/lists/impuestoContract.tpl"}    
      </div>
      </td>
	</tr>

  

</tbody>
</table>