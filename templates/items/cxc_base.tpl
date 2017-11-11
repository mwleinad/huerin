              <tr>
                <td align="center">{$fact.serie}{$fact.folio}</td>
                <td>{$fact.nameContact}</td>
                <td>{$fact.nombre}</td>
                <td align="center">{$fact.fecha}</td>
                <td align="right">${$fact.total_formato}</td>
                <td align="right">${$fact.payment}</td>
                <td align="right" style="{if $fact.saldo > 0.01}color:#930{else}color:#090{/if}">${$fact.saldo|number_format:2}</td>
                <td width="90">
                {if $fact.status == 1}
                  <img src="{$WEB_ROOT}/images/icons/details.png" class="spanDetails{if $fact.efectivo} spanEfectivo{/if}" id="{$fact.comprobanteId}" border="0" alt="Ver Detalle de Pagos" title="Ver Detalle de Pagos" style="cursor:pointer" />
                  
                  {if $fact.saldo > 0.01}
                   <a href="{$WEB_ROOT}/add-payment/{if !$fact.efectivo}id{else}isid{/if}/{$fact.comprobanteId}" target="_blank"><img src="{$WEB_ROOT}/images/dollar.png" class="" id="{$fact.comprobanteId}" border="0" alt="Agregar Pago" title="Agregar Pago" /></a>
                  {/if}

                  {if !$fact.efectivo}
                      {if $fact.version == '3.3'}
                          <a target="_blank" href="{$WEB_ROOT}/cfdi33-generate-pdf&filename=SIGN_{$fact.xml}&type=view" title="Ver PDF">
                              <img src="{$WEB_ROOT}/images/icons/ver_factura.png" height="16" width="16" border="0"/>
                          </a>
                      {else}
                          <a href="{$WEB_ROOT}/sistema/ver-pdf/item/{$fact.comprobanteId}" target="_blank"><img src="{$WEB_ROOT}/images/icons/ver_factura.png" id="{$fact.comprobanteId}" border="0" alt="Ver Factura" width="16" title="Ver Factura" /></a>
                      {/if}
                  {/if}
                {else}
                	Factura Cancelada  
                {/if}
                </td>
              </tr>

             