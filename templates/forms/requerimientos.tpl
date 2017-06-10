<table width="100%" cellpadding="0" cellspacing="0" id="boxTable">
<thead>
	<tr>
		<th align="center" colspan="2" style="border-top:1px solid #CCC">        
        <div style="float:left; margin-left:400px">REQUERIMIENTOS</div>
        <div class="iconSH" id="tbInfSubReqS" onclick="toggleSection('tbInfSubReq',1)" style="display:none">[+]</div>
        <div class="iconSH" id="tbInfSubReqH" onclick="toggleSection('tbInfSubReq',0)">[-]</div>
        </th>
	</tr>
</thead>
<tbody id="tbInfSubReq">

  <tr>
		<td align="left" width="40%" class="tdPad">
    {if $canEdit}
    	<a href="{$WEB_ROOT}/add-requerimiento/id/{$contractInfo.contractId}" onclick="return parent.GB_show('Agregar Requerimiento', this.href,200,970) "><img src="{$WEB_ROOT}/images/icons/add.png" title="Agregar Requerimiento"/> Agregar Requerimiento</a>
		{/if}
			<div id="contentRequerimientos">
			{include file="{$DOC_ROOT}/templates/lists/requerimiento.tpl"}    
      </div>
      </td>
	</tr>

  

</tbody>
</table>