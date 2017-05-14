<table width="100%" cellpadding="0" cellspacing="0" id="boxTable">
<thead>
	<tr>
		<th align="center" colspan="2" style="border-top:1px solid #CCC">        
        <div style="float:left; margin-left:400px">OBLIGACIONES</div>
        <div class="iconSH" id="tbInfSubOblS" onclick="toggleSection('tbInfSubObl',1)" style="display:none">[+]</div>
        <div class="iconSH" id="tbInfSubOblH" onclick="toggleSection('tbInfSubObl',0)">[-]</div>
        </th>
	</tr>
</thead>
<tbody id="tbInfSubObl">

  <tr>
		<td align="left" width="40%" class="tdPad">
    	<a href="{$WEB_ROOT}/add-obligacion/id/{$contractInfo.contractId}" onclick="return parent.GB_show('Agregar Obligacion', this.href,200,970) "><img src="{$WEB_ROOT}/images/icons/add.png" title="Agregar Obligacion"/> Agregar Obligacion</a>

			<div id="contentObligaciones">
			{include file="{$DOC_ROOT}/templates/lists/obligacionContract.tpl"}    
      </div>
      </td>
	</tr>

  

</tbody>
</table>