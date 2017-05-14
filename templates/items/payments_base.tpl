              <tr>
                <td align="center">{if $post.facturador=="Efectivo"}Efectivo{else}{$fact.paymentId}{/if}</td>
                <td align="center">{$fact.metodoDePago}</td>
                <td align="right">{$fact.paymentDate}</td>
                <td align="right">${$fact.amount}</td>
                <td width="90">
                   <img src="{$WEB_ROOT}/images/icons/action_delete.gif" class="spanDeletePayment" id="{$fact.paymentId}" border="0" alt="Borrar Pago" />
                   {if $fact.ext != ''}
                   <a title="Ver Comprobante de Pago" href="{$WEB_ROOT}/download.php?file=payments/{$fact.paymentId}.{$fact.ext}"><img src="{$WEB_ROOT}/images/icons/ver_factura.png" id="{$fact.comprobanteId}" border="0" alt="Ver Factura" width="16" /></a>
                   {/if}
                </td>
              </tr>
             