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
    {if in_array(68,$permissions)||$User.isRoot}
        <a href="javascript:;" data-id="{$contractInfo.contractId}" data-tipo="archivo" class="addFile"><img src="{$WEB_ROOT}/images/icons/add.png" title="Agregar archivo"/> Agregar archivo</a>
    {/if}
    <div id="contentArchivos">
        {include file="{$DOC_ROOT}/templates/lists/archivo.tpl"}
    </div>
  </td>
	</tr>

  

</tbody>
</table>