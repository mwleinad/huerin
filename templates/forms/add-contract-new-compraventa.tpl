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
        	{include file="{$DOC_ROOT}/templates/lists/documentSelladoC.tpl"}
        </td>
	</tr>
<tr>
		<td align="left" class="tdPad" width="40%">Cobrado por Roque&ntilde;i Straffon, S.C.</td>
		<td align="left" class="tdPad">
        <select name="cobradoC" id="cobradoC" class="smallInput" onchange="showCobradoC()">
        <option value="0" {if $infC.cobrado == 0}selected{/if}>No</option>
        <option value="1" {if $infC.cobrado == 1}selected{/if}>Si</option>
        </select>
        <div id="divCobradoC" {if $infC.cobrado != 1}style="display:none"{/if}>
        	<table width="180" cellspacing="0" cellpadding="0">
            <tr>
                <td align="left">
                <input name="fechaCobradoC" id="fechaCobradoC" type="text" value="{$infC.fechaCobrado}" class="smallInput small" size="50" readonly="readonly" />
                </td>
                <td align="left">
                <img src="{$WEB_ROOT}/images/icons/calendar.png" width="16" height="16" id="triggerFCob"  />
                <script type="text/javascript">
                Calendar.setup({
                    inputField : "fechaCobradoC",
                    trigger    : "triggerFCob",
					dateFormat : "%d-%m-%Y",
                    onSelect   : function() { this.hide() }
                });
                </script>
                </td>
            </tr>
            </table>
        </div>
        </td>
	</tr>
</tbody>
</table>