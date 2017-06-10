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
        {if $infC.certLibGrav == "noSolicitar"} Aun no se puede solicitar
        {elseif $infC.certLibGrav == "enTramite"} En tramite
        {elseif $infC.certLibGrav == "conCert"} Ya contamos con certificado
        {/if}
        </i>
        </td>
	</tr>
    {*
    <tr>
		<td align="left" class="tdPad" width="40%">N&uacute;mero de Certificados</td>
		<td align="left" class="tdPad"><i>{$infC.noCertificados}</i></td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Ubicaci&oacute;n del Registro P&uacute;blico de la Propiedad correspondiente</td>
		<td align="left" class="tdPad"><i>{$infC.ubicacionRpp}</i></td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Persona que ingresa el tr&aacute;mite del certificado</td>
		<td align="left" class="tdPad"><i>{$infC.nomTramitaCert}</i></td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Fecha en que se solicit&oacute; el certificado</td>
		<td align="left" class="tdPad"><i>{$infC.fechaSolCert}</i></td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Derechos por cada certificado</td>
		<td align="left" class="tdPad"><i>{$infC.derechosCert}</i></td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Honorarios o costos por cada certificado cobrados por los notarios o gestores (adicionales a los derechos)</td>
		<td align="left" class="tdPad"><i>{$infC.honorariosCert}</i></td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Fecha en que el certificado fue entregado a Roque&ntilde;i Straffon, S.C.</td>
		<td align="left" class="tdPad"><i>{$infC.fechaEntCert}</i></td>
	</tr>
    *}
    <tr>
		<td align="left" class="tdPad" width="40%">Resultado del Certificado</td>
		<td align="left" class="tdPad">
        <i>
        {if $infC.resCert == "sinGravamen"} Sin gravamen
        {elseif $infC.resCert == "conGravamen"} Con gravamen
        {/if}
        <br />
        {if $infC.comentarios == 1} <br /> <b>Comentarios:</b>
        {elseif $infC.comentarios == 0} Sin comentarios
        {/if}
        
        {if $infC.comentarios == 1}
        <br />
        {$infC.comentario}
        {/if}
        </i>
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Enviado el certificado a Walmart</td>
		<td align="left" class="tdPad">
        {if $infC.certEnviado == 1} Si
        {elseif $infC.certEnviado == 0} No
        {/if}
        
        {if $infC.certEnviado == 1}
        	<br /><br />
        	{$infC.fechaCertEnv}
        {/if}
        </td>
	</tr>
</tbody>
</table>

<table width="100%" cellpadding="0" cellspacing="0" id="boxTable">
<thead>
	<tr>
		<th align="center" colspan="3" style="border-top:1px solid #CCC">        
        <div style="float:left; margin-left:350px">CONTROL DE LA COMPRAVENTA</div>
        <div class="iconSH" id="tbInfoProyCVtaS" onclick="toggleSection('tbInfoProyCVta',1)" style="display:none">[+]</div>
        <div class="iconSH" id="tbInfoProyCVtaH" onclick="toggleSection('tbInfoProyCVta',0)">[-]</div>
        </th>
	</tr>
</thead>
<tbody id="tbInfoProyCVta">     
    <tr>
		<td align="left" class="tdPad" width="40%">Persona</td>
		<td align="left" class="tdPad">
        <i>
        {if $infC.persona == "f"} Fisica 
        {elseif $infC.persona == "m"} Moral 
        {/if}
        
        {if $infC.persona == "f"}
            <br /><br />
            <b>Estado Civil:</b> 
            {if $infC.edoCivil == "s"} Soltero
            {elseif $infC.edoCivil == "c"} Casado bajo el regimen de separaci&oacute;n de bienes
            {elseif $infC.edoCivil == "r"} Casado bajo el regimen de sociedad conyugal
            {/if}
            <br />
        	<b>Acta de Matrimonio:</b>
       		{if $infC.actaMat == 1}	Si
            {elseif $infC.actaMat == 0}	Pendiente
            {elseif $infC.actaMat == 2}	No aplica
            {/if}
        	<br />
        	<b>Convenio de Divorcio:</b>
        	{if $infC.conDiv == 1} Si
            {elseif $infC.conDiv == 0} Pendiente
            {elseif $infC.conDiv == 2} No aplica
            {/if}
        {/if}
        
        {if $infC.persona == "m"}
            <br /><br />
            <b>Constitutiva:</b>
            {if $infC.constitutiva == 1} Si
            {elseif $infC.constitutiva == 0} Pendiente
            {/if}
            <br />
            <b>Modificaci&oacute;n a estatutos necesaria:</b>
            {if $infC.modEstatutos == 1} Si
            {elseif $infC.modEstatutos == 0} Pendiente
            {elseif $infC.modEstatutos == 2} No aplica
            {/if}
            <br />
            <b>Poder:</b>
            {if $infC.poder == 1} Si
            {elseif $infC.poder == 0} Pendiente
            {/if}
        {/if}
        </i>
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">RFC</td>
		<td align="left" class="tdPad">
        <i>
        {if $infC.rfc == 1}	Si
        {elseif $infC.rfc == 0} Pendiente
        {/if}
        </i>
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Predial</td>
		<td align="left" class="tdPad">       
        <i>
        {if $infC.predial == 1} Si
        {elseif $infC.predial == 0}	Pendiente
        {/if}
        
        {if $infC.predial == 1}
        	<br />
        	<b>Pagado hasta:</b> {$infC.fechaPredial}
		{/if}
        </i>
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Agua</td>
		<td align="left" class="tdPad">
        <i>
        {if $infC.agua == 1} Si
        {elseif $infC.agua == 0} Pendiente
        {elseif $infC.agua == 2} El inmueble no cuenta con toma de agua ni servicio contratado
        {/if}
        
        {if $infC.agua == 1}
        	<b>Pagado hasta:</b> {$infC.fechaAgua}
        {/if}
        </i>
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Propiedad acreditada</td>
		<td align="left" class="tdPad">
        <i>
        {if $infC.propAcreditada == 1} Si
        {elseif $infC.propAcreditada == 0} No
        {/if}
        </i>
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">T&iacute;tulo de Propiedad con datos de inscripci&oacute;n</td>
		<td align="left" class="tdPad">
        <i>
        {if $infC.titProp == 1} Si
        {elseif $infC.titProp == 0} No
        {/if}
        </i>
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Se requiere fusionar o subdividir el inmueble de forma previa a la compra</td>
		<td align="left" class="tdPad">
        <i>
        {if $infC.subdivInm == 1} Si
        {elseif $infC.subdivInm == 0} No aplica por tratarse de un solo inmueble
        {elseif $infC.subdivInm == 2} No es necesario, Walmart fusionar&aacute; de forma posterior a la compra
        {/if}
        
        {if $infC.subdivInm == 1}
            <br />
            <b>&iquest;Cu&aacute;ndo se realizar&aacute; dicha fusi&oacute;n o subdivisi&oacute;n?</b>
            <br />
            {if $infC.cuandoDivInm == 1} Previamente
            {elseif $infC.cuandoDivInm == 0} Simult&aacute;neamente
            {/if}
        {/if}
        </i>
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Fraccionamiento o Conjunto Urbano</td>
		<td align="left" class="tdPad">
        <i>
        {if $infC.fraccionamiento == 1} Si aplica
        {elseif $infC.fraccionamiento == 0} No aplica
        {/if}
        
        {if $infC.fraccionamiento == 1}
            <br /><br />
            <b>Descripci&oacute;n legal del inmueble:</b>
            {if $infC.descInm == 1} Si
            {elseif $infC.descInm == 0} Pendiente
            {elseif $infC.descInm == 2} WM autoriz&oacute; adquirir sin este requisito
            {/if}
            <br />
            <b>Autorizaci&oacute;n del Fraccionamiento:</b>
            {if $infC.autFracc == 1} Si
            {elseif $infC.autFracc == 0} Pendiente
            {elseif $infC.autFracc == 2} WM autoriz&oacute; adquirir sin este requisito
            {/if}
            <br />
            <b>Autorizaci&oacute;n de enajenaci&oacute;n:</b>
            {if $infC.autEnajenacion == 1} Si
            {elseif $infC.autEnajenacion == 0} Pendiente
            {elseif $infC.autEnajenacion == 2} Pendiente la protocolizaci&oacute;n
            {elseif $infC.autEnajenacion == 3} No aplica
            {elseif $infC.autEnajenacion == 4} WM autoriz&oacute; adquirir sin este requisito
            {/if}
            <br />
            <b>Transmisi&oacute;n de las &aacute;reas de Donaci&oacute;n:</b>        
            {if $infC.transHas == 1} Si
            {elseif $infC.transHas == 0} Pendiente
            {elseif $infC.transHas == 2} No aplica
            {elseif $infC.transHas == 3} WM autoriz&oacute; adquirir sin este requisito
            {/if}
            <br />
            <b>Licencia de Construcci&oacute;n:</b>
            {if $infC.licConst == 1} Si
            {elseif $infC.licConst == 0} Pendiente
            {elseif $infC.licConst == 2} No aplica
            {elseif $infC.licConst == 3} WM autoriz&oacute; adquirir sin este requisito
            {/if}
            <br />
            <b>Municipalizaci&oacute;n de las obras de urbanizaci&oacute;n:</b>
            {if $infC.munObras == 1} Si
            {elseif $infC.munObras == 0} Pendiente
            {elseif $infC.munObras == 2} No aplica
            {elseif $infC.munObras == 3} WM autoriz&oacute; adquirir sin este requisito
            {/if}        
        {/if}
        </i>
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Uso del suelo general</td>
		<td align="left" class="tdPad">
        <i>
        {if $infC.usoSuelo == 1} Si
        {elseif $infC.usoSuelo == 0} Pendiente a cargo de Walmart
        {elseif $infC.usoSuelo == 2} Pendiente a cargo del propietario
        {/if}
        </i>
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Uso del suelo espec&iacute;fico</td>
		<td align="left" class="tdPad">
        <i>
        {if $infC.usoSueloEsp == 1} Si
        {elseif $infC.usoSueloEsp == 0} Pendiente a cargo de Walmart
        {elseif $infC.usoSueloEsp == 2} Pendiente a cargo del propietario
        {elseif $infC.usoSueloEsp == 3} No aplica
        {/if}
        </i>
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Agenda</td>
		<td align="left" class="tdPad">
        <i>
        {if $infC.agenda == 1} Si
        {elseif $infC.agenda == 0} Pendiente
        {/if}
        </i>
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Precio</td>
		<td align="left" class="tdPad">
        <i>
        {if $infC.precio == 1} Si Confirmado
        {elseif $infC.precio == 0} Pendiente
        {/if}
        </i>
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Comparativa</td>
		<td align="left" class="tdPad">
        <i>
        {if $infC.comparativa == 1} Si
        {elseif $infC.comparativa == 2} Se requiere una nueva comparativa
        {elseif $infC.comparativa == 3} La comparativa se&ntilde;ala problemas significativos
        {elseif $infC.comparativa == 0} Pendiente
        {/if}
        </i>
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Construcciones</td>
		<td align="left" class="tdPad">
        <i>
        {if $infC.construcciones == 1} Si
        {elseif $infC.construcciones == 0} No
        {/if}
        </i>
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Certificaciones</td>
		<td align="left" class="tdPad">
        <i>
        {if $infC.certificaciones == 1} Si
        {elseif $infC.certificaciones == 0} En tr&aacute;mite
        {elseif $infC.certificaciones == 2} Pendiente
        {/if}
        </i>
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Avaluo</td>
		<td align="left" class="tdPad">
        <i>
        {if $infC.avaluo == 1} Si
        {elseif $infC.avaluo == 0} En tr&aacute;mite
        {/if}
        </i>
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Notario asignado</td>
		<td align="left" class="tdPad"><i>{$infC.notario}</i></td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Proyecto de escritura</td>
		<td align="left" class="tdPad">
        <i>
        {if $infC.proyEscritura == 0} Pendiente
        {elseif $infC.proyEscritura == 1} Preeliminar
        {elseif $infC.proyEscritura == 2} Definitivo
        {/if}
        
        {if $infC.proyEscritura != 0}
        	<br /><br />
        	<b>Fecha de Envio:</b> {$infC.fechaProyEsc}
        {/if}            
        </i>
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Proyecto de escritura aprobado por la parte vendedora</td>
		<td align="left" class="tdPad">
        <i>
        {if $infC.proyEscrAprobado == 1} Si
        {elseif $infC.proyEscrAprobado == 0} En tr&aacute;mite
        {/if}
        </i>
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Calculo de ISR</td>
		<td align="left" class="tdPad">
        <i>
        {if $infC.calculoIsr == 0} Pendiente de solicitar
        {elseif $infC.calculoIsr == 3} Enviado
        {elseif $infC.calculoIsr == 1} Si, aprobado por la parte vendedora
        {elseif $infC.calculoIsr == 2} No aplica
        {/if}
        
        {if $infC.calculoIsr == 3}
        	<br /><br />
        	<b>Fecha de Envio:</b> {$infC.fechaCalcIsr}
        {/if}
        </i>
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Calculo de IVA</td>
		<td align="left" class="tdPad">
        <i>
        {if $infC.calculoIva == 0} Pendiente de solicitar
        {elseif $infC.calculoIva == 3} Enviado
        {elseif $infC.calculoIva == 1} Si, aprobado por la parte vendedora
        {elseif $infC.calculoIva == 2} No aplica
        {/if}
        
        {if $infC.calculoIva == 3}
        	<br /><br />
        	<b>Fecha de Envio:</b> {$infC.fechaCalcIva}
        {/if}
        </i>
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Cheques</td>
		<td align="left" class="tdPad">
        <i>
        {if $infC.cheques == 0} Pendiente
        {elseif $infC.cheques == 2} Solicitados
        {elseif $infC.cheques == 1} Si, cheques listos
        {/if}
        
        {if $infC.cheques == 2}
        	<br /><br />
        	<b>Fecha de Solicitud:</b> {$infC.fechaCheques}
        {/if}
        </i>
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Fecha de celebraci&oacute;n de la compraventa</td>
		<td align="left" class="tdPad">
        <i>
        {if $infC.fechaCompVta == "firmado"} Firmado
        {elseif $infC.fechaCompVta == "pendiente"} Pendiente
        {/if}
        
        {if $infC.fechaCompVta == "firmado"}
        	<br /><br />
            {$infC.fechaCVta}
        {/if}
        </i>
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Fecha limite para pagar los impuestos sin generar recargos ni multas</td>
		<td align="left" class="tdPad"><i>{$infC.fechaPagoImp}</i></td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Requerimientos para pago de impuestos, derechos y honorarios</td>
		<td align="left" class="tdPad">
        <i>
        <b>C&aacute;lculo Impuestos y Derechos:</b>
        {if $infC.calcImpDer == 1} Si
        {elseif $infC.calcImpDer == 0} Pendiente
        {elseif $infC.calcImpDer == 2} No aplica
        {/if}
        <br />
        <b>Copia Certificada de Escritura:</b>
        {if $infC.copiaCertEsc == 1} Si
        {elseif $infC.copiaCertEsc == 0} Pendiente
        {elseif $infC.copiaCertEsc == 2} No aplica
        {/if}
        <br />
        <b>Avaluo catastral o comercial seg&uacute;n aplique:</b>
        {if $infC.avaluoCatastral == 1} Si
        {elseif $infC.avaluoCatastral == 0} Pendiente
        {elseif $infC.avaluoCatastral == 2} No aplica
        {/if}
        <br />
        <b>Ultimo pago de impuestos predial:</b>
        {if $infC.ultPagoPredial == 1} Si
        {elseif $infC.ultPagoPredial == 0} Pendiente
        {elseif $infC.ultPagoPredial == 2} No aplica
        {/if}
        <br />
        <b>Boleta de toma de agua:</b>
        {if $infC.boletaAgua == 1} Si
        {elseif $infC.boletaAgua == 0} Pendiente
        {elseif $infC.boletaAgua == 2} No aplica
        {/if}
        <br />
        <b>Si el inmueble proviene de subdivisiones y/o fusiones los oficios correspondientes:</b>
        {if $infC.inmProvSubdiv == 1} Si
        {elseif $infC.inmProvSubdiv == 0} Pendiente
        {elseif $infC.inmProvSubdiv == 2} No aplica
        {/if}
        <i>
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Factura de la Notaria entregada a Walmart</td>
		<td align="left" class="tdPad">
        <i>
        {if $infC.factNotaria == 0} Pendiente
        {elseif $infC.factNotaria == 1} Entregada
        {/if}
        </i>
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Pago de impuestos y derechos por parte de Walmart a la Notaria</td>
		<td align="left" class="tdPad">
        <i>
        {if $infC.pagoImpWal == 0} Pendiente
        {elseif $infC.pagoImpWal == 2} En proceso
        {elseif $infC.pagoImpWal == 1} Pagado
        {/if}
        
        {if $infC.pagoImpWal == 1}
        	<br /><br />
            {$infC.fechaImpWal}
        {/if}
        </i>
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Comprobacion del pago de impuestos y derechos realizado por parte de la Notaria</td>
		<td align="left" class="tdPad">
        <i>
        {if $infC.compPagoImpNot == 0} Pendiente
        {elseif $infC.compPagoImpNot == 2} En proceso
        {elseif $infC.compPagoImpNot == 1} Comprobado
        {/if}
        
        {if $infC.compPagoImpNot == 1}
        	<br /><br />
        	{$infC.fechaImpNot}
        {/if}
        </i>
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Pago de honorarios por parte de Walmart a la Notaria</td>
		<td align="left" class="tdPad">
        <i>
        {if $infC.pagoHonorarios == 0} Pendiente
        {elseif $infC.pagoHonorarios == 2} En proceso
        {elseif $infC.pagoHonorarios == 1} Pagado
        {/if}
        
        {if $infC.pagoHonorarios == 1}
        	<br /><br />
        	{$infC.fechaHonorarios}
        {/if}
        </i>
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Alguna diferencia por pagar o comprobar por parte de Walmart o de la Notaria respectivamente</td>
		<td align="left" class="tdPad">
        <i>
        {if $infC.pagoPteWal == 0} No aplica
        {elseif $infC.pagoPteWal == 1} Si aplica
        {/if}
        
        {if $infC.pagoPteWal == 1}
        	<br /><br />
        	{$infC.descPagoPteWal}
        {/if}
        </i>
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Escritura debidamente inscrita en el Registro P&uacute;blico de la Propiedad entregada a Walmart</td>
		<td align="left" class="tdPad">
        <i>
        {if $infC.escRpp == 0} Pendiente de inscripci&oacute;n
        {elseif $infC.escRpp == 1} Entregada a Walmart
        {/if}
        
        {if $infC.escRpp == 1}
        	<br /><br />
            {$infC.fechaEscRpp}
        {/if}
        </i>
        </td>
	</tr>
</tbody>
</table>