              <tr>
                <td style="width: auto;">{$fact.serie}</td>
                <td align="center">{$fact.folio}</td>
                <td align="center">{if $fact.status eq 1}Activo{else}Cancelado{/if}</td>
                <td align="center">{$fact.concepto}</td>
                <td>{$fact.nombre}</td>
                <td align="center">{$fact.fecha}</td>
                <td align="center">{if $fact.status eq 0}{$fact.fechaPedimento}{else}N/A{/if}</td>
                <td>${$fact.subtotal_formato}</td>
                <td>${$fact.iva_formato}</td>
                <td>${$fact.total_formato}</td>
                <td align="center">{$fact.version}</td>
                <td align="center">{$fact.xml}</td>
                <!-- <td style="width:100px; word-wrap:break-word;">{$fact.uuid|wordwrap:20:"\n":true}</td>-->
              </tr>
             