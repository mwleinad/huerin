<table border="1">
    <thead>
    <tr>
        <th style="background:#E0E5E7;text-align:center"><b>#</b></th>
        <th style="background:#E0E5E7;text-align:center"><b>CLIENTE</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>NOMBRE RAZON SOCIAL</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>EMAIL CONT. ADMINISTRATIVO RAZON</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>RESPONSABLE DE ADMINISTRACION</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>NUMERO DE FACT. PENDIENTES</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>FACT. DETALLES</b></th>
        <th style="background:#D7EBFF;text-align:center;"><b>MONTO TOTAL ADEUDO</b></th>
    </tr>
    </thead>
    <tbody>
    {foreach from=$contracts key=key item=item}
    <tr>
        <td style="text-align:left;">{$key+1}</td>s
        <td style="text-align:left;">{$item.nameContact}</td>
        <td style="text-align:center;">{$item.name}</td>
        <td style="text-align:center;">{$item.emailContactoAdministrativo}</td>
        <td style="text-align:center;">{$item.nameAdministracion}</td>
        <td style="text-align:center;">{$item.numeroFactura}</td>
        <td style="text-align:left; ">
            {foreach from=$item.factPendientes key=kfact item=fact}
                <b>FOLIO:</b> {$fact.folioSerie}    <b>MONTO:</b> $ {$fact.pendiente|number_format:2:'.':','}  <b>FECHA-EMISION:</b>{$fact.fecha|date_format:'%Y-%m-%d'}<hr/>
            {/foreach}
        </td>
        <td style="text-align:left;">{$item.montoTotal}</td>
    </tr>
    {foreachelse}
      <tr>
          <td colspan="5">No se encontraron resultados</td>
      </tr>
    {/foreach}
    </tbody>
</table>   