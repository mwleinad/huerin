<table border="1">
    <thead>
    <tr>
        <th style="background:#E0E5E7;text-align:center"><b>NO. CLIENTE</b></th>
        <th style="background:#E0E5E7;text-align:center"><b>NO. CONTRATO</b></th>
        <th style="background:#E0E5E7;text-align:center"><b>CLIENTE</b></th>
        <th style="background:#E0E5E7;text-align:center"><b>TEL. CONTACTO</b></th>
        <th style="background:#E0E5E7;text-align:center"><b>EMAIL CONTACTO</b></th>
        <th style="background:#E0E5E7;text-align:center"><b>PASSWORD</b></th>
        <th style="background:#E0E5E7;text-align:center"><b>RAZONES SOCIALES</b></th>
        <th style="background:#E0E5E7;text-align:center"><b>FECHA ALTA</b></th>
        <th style="background:#E0E5E7;text-align:center"><b>OBSERVACIONES</b></th>
        <th style="background:#E0E5E7;text-align:center"><b>CLIENTE ACTIVO</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>NOMBRE RAZON SOCIAL</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>TOTAL DE IGUALA</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>TIPO</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>RFC</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>REGIMEN FISCAL</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>RAZON ACTIVA</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>NOMBRE COMERCIAL</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>DIRECCION COMERCIAL</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>DIRECCION FISCAL</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>NOMBRE CONTACTO ADMINISTRATIVO</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>EMAIL CONTACTO ADMINISTRATIVO</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>TELEFONO CONTACTO ADMINISTRATIVO</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>NOMBRE CONTACTO CONTABILIDAD</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>EMAIL CONTACTO CONTABILIDAD</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>TELEFONO CONTACTO CONTABILIDAD</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>NOMBRE CONTACTO DIRECTIVO</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>EMAIL CONTACTO DIRECTIVO</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>TELEFONO CONTACTO DIRECTIVO</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>CELULAR CONTACTO DIRECTIVO</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>CLAVE CIEC</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>CLAVE FIEL</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>CLAVE IDSE</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>CLAVE ISN</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>FACTURADOR</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>METODO DE PAGO</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>NUMERO DE CUENTA</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>RESPONSABLE</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>SUPERVISOR</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>RESP. CONTABILIDAD</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>RESP. NOMINA</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>RESP. ADMIN</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>RESP. JURIDICO</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>RESP. IMSS</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>RESP. MENSAJERIA</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>RESP. AUDITORIA</b></th>

    </tr>
    </thead>
    <tbody>
    {foreach from=$customers key=key item=item}
    <tr>
        <td style="text-align:center;">{$item['customerId']}</td>
        <td style="text-align:center;">{$item['contractId']}</td>
        <td style="text-align:left;">{($item['nameContact'])}</td>
        <td style="text-align:center;">{$item['customerPhone']}</td>
        <td style="text-align:left;">{$item['customerEmail']}</td>
        <td style="text-align:center;">{$item['password']}</td>
        <td style="text-align:center;">{$item['totalContracts']}</td>
        <td style="text-align:center;">{date('d-m-Y',strtotime($item['fechaAlta']))}</td>
        <td style="text-align:center;">{($item['observaciones'])}</td>
        <td style="text-align:center;">{if $item['customerActive'] eq 1}Si{else}No{/if}</td>
        <td style="text-align:center;">{($item['name'])}</td>
        <td style="text-align:center;">${$item['totalMensual']}</td>
        <td style="text-align:center;">{($item['type'])}</td>
        <td style="text-align:center;">{$item['rfc']}</td>
        <td style="text-align:center;">{$item['nomRegimen']}</td>
        <td style="text-align:center;">{$item['activo']}</td>
        <td style="text-align:center;">{$item['nombreComercial']}</td>
        <td style="text-align:center;">{$item['direccionComercial']}</td>
        <td style="text-align:center;">{$item['address']} {$item['noExtAddress']} {$item['noIntAddress']} {$item['coloniaAddress']}
            {$item['municipioAddress']} {$item['estadoAddress']} {$item['cpAddress']}</td>
        <td style="text-align:center;">{$item['nameContactoAdministrativo']}</td>
        <td style="text-align:center;">{$item['emailContactoAdministrativo']}</td>
        <td style="text-align:center;">{$item['telefonoContactoAdministrativo']}</td>
        <td style="text-align:center;">{$item['nameContactoContabilidad']}</td>
        <td style="text-align:center;">{$item['emailContactoContabilidad']}</td>
        <td style="text-align:center;">{$item['telefonoContactoContabilidad']}</td>
        <td style="text-align:center;">{$item['nameContactoDirectivo']}</td>
        <td style="text-align:center;">{$item['emailContactoDirectivo']}</td>
        <td style="text-align:center;">{$item['telefonoContactoDirectivo']}</td>
        <td style="text-align:center;">{$item['telefonoCelularDirectivo']}</td>
        <td style="text-align:center;">{$item['claveCiec']}</td>
        <td style="text-align:center;">{$item['claveFiel']}</td>
        <td style="text-align:center;">{$item['claveIdse']}</td>
        <td style="text-align:center;">{$item['claveIsn']}</td>
        <td style="text-align:center;">{$item['facturador']}</td>
        <td style="text-align:center;">{$item['metodoDePago']}</td>
        <td style="text-align:center;">{$item['noCuenta']}</td>
        <td style="text-align:center;">{$item['nameResponsableCuenta']}</td>
        <td style="text-align:center;">{$item['supervisadoBy']}</td>
        <td style="text-align:center;">{$item['nameContabilidad']}</td>
        <td style="text-align:center;">{$item['nameNominas']}</td>
        <td style="text-align:center;">{$item['nameAdministracion']}</td>
        <td style="text-align:center;">{$item['nameJuridico']}</td>
        <td style="text-align:center;">{$item['nameImss']}</td>
        <td style="text-align:center;">{$item['nameMensajeria']}</td>
        <td style="text-align:center;">{$item['nameAuditoria']}</td>
    </tr>
    {foreachelse}
      <tr>
          <td colspan="12">No se encontraron resultados</td>
      </tr>
    {/foreach}
    </tbody>
</table>   