<tr>
    <td align="center">{$fact.receptorRfc}</td>
    <td align="center">{$fact.receptorName}</td>
    <td align="center">{$fact.fecha}</td>
    <td align="center">{$fact.folio}</td>
    <td align="center">{$fact.uuid}</td>
    <td align="center">${$fact.total|number_format:2:'.':','}</td>
    <td align="center">${$fact.pagos|number_format:2:'.':','}</td>
    <td align="center">${$fact.saldo|number_format:2:'.':','}</td>
    <td width="90 "  align="center">
        <a target="_blank" href="{$WEB_ROOT}/cfdi33-generate-pdf&filename={$fact.nameXml}&type=view" title="Ver PDF">
            <img src="{$WEB_ROOT}/images/icons/ver_factura.png" height="16" width="16" border="0"/>
        </a>
        <a href="javascript:;" target="_blank">
            <img src="{$WEB_ROOT}/images/dollar.png" class="spanAll spanAddPayment" data-namexml="{$fact.nameXml}" border="0" alt="agregar pago" title="Agregar pago" />
        </a>

    </td>
</tr>

             