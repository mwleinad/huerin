              <tr>
                <td width="34">{$fact.rfc}</td>
                <td>{$fact.nombre}</td>
                <td align="center">{$fact.fecha}</td>
                <td>${$fact.subtotal_formato}</td>
                <td>${$fact.iva_formato}</td>
                <td>${$fact.total_formato}</td>
                <td align="center">{$fact.serie}{$fact.folio}</td>
                <td align="center">
                {if $fact.instanciaServicioId}
                	<a href="{$WEB_ROOT}/workflow/id/{$fact.instanciaServicioId}">{$fact.instanciaServicioId} - Ir</a>
                {else}
                	Factura Manual
                {/if}  </td>
                <td style="width:100px; word-wrap:break-word;">{$fact.uuid|wordwrap:20:"\n":true}</td>
                <td width="90"><a href="javascript:void(0)">
    						<img src="{$WEB_ROOT}/images/icons/details.png" class="spanDetails" id="{$fact.comprobanteId}" border="0" alt="Ver Detalles" /></a>{if $fact.status == 1}<a href="javascript:void(0)"><img src="{$WEB_ROOT}/images/icons/cancel.png" class="spanCancel" id="{$fact.comprobanteId}" border="0" alt="Cancelar"/></a>{/if}</td>
              </tr>
             