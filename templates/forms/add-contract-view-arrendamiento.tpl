<table width="100%" cellpadding="0" cellspacing="0" id="boxTable">
<thead>
	<tr>
		<th align="center" colspan="2" style="border-top:1px solid #CCC">        
        <div style="float:left; margin-left:250px">SUBCONTROL PARA LOS CERTIFICADOS DE LIBERTAD DE GRAVAMEN</div>
        <div class="iconSH" id="tbInfSubS" onclick="toggleSection('tbInfSub',1)" style="display:none">[+]</div>
        <div class="iconSH" id="tbInfSubH" onclick="toggleSection('tbInfSub',0)">[-]</div>
        </th>
	</tr>
</thead>
<tbody id="tbInfSub">
    <tr>
		<td align="left" class="tdPad" width="40%">Certificado de Libertad de Gravamenes</td>
		<td align="left" class="tdPad">
        <i>
        {if $infA.certLibGrav == "noSolicitar"} Aun no se puede solicitar
        {elseif $infA.certLibGrav == "enTramite"} En tramite
        {elseif $infA.certLibGrav == "conCert"} Ya contamos con certificado
        {/if}
        </i>
        </td>
	</tr>
    {*
    <tr>
		<td align="left" class="tdPad" width="40%">N&uacute;mero de Certificados</td>
		<td align="left" class="tdPad"><i>{$infA.noCertificados}</i></td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Ubicaci&oacute;n del Registro P&uacute;blico de la Propiedad correspondiente</td>
		<td align="left" class="tdPad"><i>{$infA.ubicacionRpp}</i></td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Persona que ingresa el tr&aacute;mite del certificado</td>
		<td align="left" class="tdPad"><i>{$infA.nomTramitaCert}</i></td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Fecha en que se solicit&oacute; el certificado</td>
		<td align="left" class="tdPad"><i>{$infA.fechaSolCert}</i></td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Derechos por cada certificado</td>
		<td align="left" class="tdPad"><i>{$infA.derechosCert}</i></td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Honorarios o costos por cada certificado cobrados por los notarios o gestores (adicionales a los derechos)</td>
		<td align="left" class="tdPad"><i>{$infA.honorariosCert}</i></td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Fecha en que el certificado fue entregado a Roque&ntilde;i Straffon, S.C.</td>
		<td align="left" class="tdPad"><i>{$infA.fechaEntCert}</i></td>
	</tr>
    *}
    <tr>
		<td align="left" class="tdPad" width="40%">Resultado del Certificado</td>
		<td align="left" class="tdPad">
        <i>
        {if $infA.resCert == "sinGravamen"} Sin gravamen
        {elseif $infA.resCert == "conGravamen"} Con gravamen
        {/if}
        <br />
        {if $infA.comentarios == 1} <br /> <b>Comentarios:</b>
        {elseif $infA.comentarios == 0} Sin comentarios
        {/if}
        
        {if $infA.comentarios == 1}
        <br />
        {$infA.comentario}
        {/if}
        </i>
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Enviado el certificado a Walmart</td>
		<td align="left" class="tdPad">
        {if $infA.certEnviado == 1} Si
        {elseif $infA.certEnviado == 0} No
        {/if}
        
        {if $infA.certEnviado == 1}
        	<br /><br />
        	{$infA.fechaCertEnv}
        {/if}
        </td>
	</tr>
</tbody>
</table>

<table width="100%" cellpadding="0" cellspacing="0" id="boxTable">
<thead>
	<tr>
		<th align="center" colspan="2" style="border-top:1px solid #CCC">        
        <div style="float:left; margin-left:320px">CONTROL ADMINISTRATIVO INTERNO</div>
        <div class="iconSH" id="tbInfAdmS" onclick="toggleSection('tbInfAdm',1)" style="display:none">[+]</div>
        <div class="iconSH" id="tbInfAdmH" onclick="toggleSection('tbInfAdm',0)">[-]</div>
        </th>
	</tr>
</thead>
<tbody id="tbInfAdm">
<tr>
		<td align="left" class="tdPad" width="40%">Fecha de env&iacute;o del documento sellado y rubricado por parte de Roque&ntilde;i Straffon, S.C.</td>
		<td align="left" class="tdPad">
        	
            {include file="{$DOC_ROOT}/templates/lists/documentSelladoView.tpl"}
        
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Cobrado por Roque&ntilde;i Straffon S.C.</td>
		<td align="left" class="tdPad">
        {if $infA.cobrado == 1} Si
        {elseif $infA.cobrado == 0} No
        {/if}
        
        {if $infA.cobrado == 1}
        	<br /><br />
        	{$infA.fechaCobrado}
        {/if}
        </td>
	</tr>
</tbody>
</table>