<table width="100%" cellpadding="0" cellspacing="0" id="boxTable">
<thead>
	<tr>
		<th align="center" colspan="2" style="border-top:1px solid #CCC">        
        <div style="float:left; margin-left:350px">CONTROL ADMINISTRATIVO INTERNO</div>
        <div class="iconSH" id="tbContAdmS" onclick="toggleSection('tbContAdm',1)" style="display:none">[+]</div>
        <div class="iconSH" id="tbContAdmH" onclick="toggleSection('tbContAdm',0)">[-]</div>
        </th>
	</tr>
</thead>
<tbody id="tbContAdm">
 <tr>
		<td align="left" class="tdPad" width="40%">Fecha de env&iacute;o del documento sellado y rubricado por parte de Roque&ntilde;i Straffon, S.C.</td>
		<td align="left" class="tdPad">
        	{include file="{$DOC_ROOT}/templates/lists/documentSelladoCView.tpl"}
        </td>
	</tr>
<tr>
		<td align="left" class="tdPad" width="40%">Cobrado por Roque&ntilde;i Straffon, S.C.</td>
		<td align="left" class="tdPad">
        <i>
        {if $infC.cobrado == 0} No
        {elseif $infC.cobrado == 1} Si
        {/if}
        
        {if $infC.cobrado == 1}
        	<br /><br />
        	{$infC.fechaCobrado}
        {/if}
        </i>
        </td>
	</tr>
</tbody>
</table>