  <tr>
    <td align="center">{if $fact.folio eq ""}N/A{else}{$fact.folio}{/if}</td>
    <td align="center">{$fact.metodoDePago}</td>
    <td align="right">{$fact.paymentDate}</td>
    <td align="right">${$fact.amount}</td>
    <td align="right">{$fact.payment_status|ucfirst}</td>
    <td width="90">
       {if in_array(126,$permissions) || $User.isRoot}
        {if $fact.payment_status eq  'activo'}
            <img src="{$WEB_ROOT}/images/icons/action_delete.gif" class="spanAll spanDeletePayment" id="{$fact.payment_id}" border="0" alt="Borrar Pago" />
        {/if}
        {/if}
       {if $fact.ext != '' && (in_array(127,$permissions) || $User.isRoot)}
        <a title="Ver Comprobante de Pago" href="{$WEB_ROOT}/download.php?file=payments/from_xml_{$fact.payment_id}.{$fact.ext}"><img src="{$WEB_ROOT}/images/icons/ver_factura.png" id="{$fact.comprobanteId}" border="0" alt="Ver Factura" width="16" /></a>
       {/if}
        {if $fact.comprobantePagoId}
            {*descargar xml*}
            {if in_array(128,$permissions) || $User.isRoot}
            <a target="_blank" href="{$WEB_ROOT}/cfdi33-generate-pdf&filename=UID_{$fact.comprobantePagoId}&type=view">
                <img src="{$WEB_ROOT}/images/pdf_icon.png" height="16" width="16" border="0" title="Descargar PDF"/>
            </a>
            {/if}
            {*descargar xml*}
            {if in_array(129,$permissions) || $User.isRoot}
                <a href="{$WEB_ROOT}/sistema/descargar-xml/item/{$fact.comprobantePagoId}">
                    <img src="{$WEB_ROOT}/images/icons/descargar.png" border="0" width="16" />
                </a>
            {/if}
        {/if}
    </td>
  </tr>
             