              <tr>
                <td width="34">{$fact.rfc}</td>
                <td>{$fact.nombre}</td>
                <td>{$fact.fecha}</td>
                <td>{$fact.total_formato}</td>
                <td>{$fact.serie}{$fact.folio}</td>
                {if $info.version == "construc" || $info.version == "v3"}
                <td>{$fact.uuid}</td>
                {/if}
                <td width="90">{$fact.comprobanteId}<a href="javascript:void(0)">
    	<img src="{$WEB_ROOT}/images/icons/details.png" class="spanDetails" id="{$fact.comprobanteId}" border="0" alt="Ver Detalles" /></a>{if $fact.status == 1}<a href="javascript:void(0)"><img src="{$WEB_ROOT}/images/icons/cancel.png" class="spanCancel" id="{$fact.comprobanteId}" border="0" alt="Cancelar"/></a>{/if}</td>
              </tr>
             