              <tr>
                <td align="center">{$fact.rfc}</td>
                <td align="center">{$fact.razonSocial}</td>
                <td align="center">{$fact.estado}</td>
                <td align="center">{$fact.cantidad}</td>
                <td align="center">${$fact.monto}</td>
                <td align="center">{$fact.fecha}</td>
                <td align="center">{$fact.metodoPago}</td>
                <td align="center">{$fact.Banco}</td>
                <td align="center">{$fact.autorizacion}</td>
                <td width="90 "  align="center">
                {if $fact.status == "noPagado"}
                    Timbres sin Autorizar
                	{*<a href="{$WEB_ROOT}/cfdiPagos/id/{$fact.idVenta}">Autorizar y Generar Factura</a>*}
                {else}
                	Timbres Autorizados
                {/if}
                </td>
              </tr>

             