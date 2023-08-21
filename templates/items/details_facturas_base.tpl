              <tr>
                <td align="center">{$fact.serie}{$fact.folio}</td>
                <td align="center">{if $fact.procedencia eq 'manual'}Factura manual{else}Factura automatica{/if}</td>
                <td align="center">{$fact.fecha|date_format:'%d-%m-%Y'}</td>
                <td align="center">$ {$fact.total|number_format:2}</td>
                <td align="right">{if $fact.status eq '1'}Activa{else}Cancelado{/if}</td>
                <td align="right">${$fact.totalPagos|number_format:2}</td>
                <td align="right">{if $fact.status eq '1'}{$fact.pagado}{else}Cancelado{/if}</td>
                <td width="90">
                        {*descargar xml*}
                        {*if in_array(128,$permissions) || $User.isRoot*}
                            {if $fact.version|in_array:['3.3','4.0']}
                                <a target="_blank" href="{$WEB_ROOT}/cfdi33-generate-pdf&identifier={$fact.comprobanteId}&type=view" class="spanAll">
                                    <img src="{$WEB_ROOT}/images/icons/pdf-18.png"  title="Ver factura"/>
                                </a>
                            {else}
                                <a href="{$WEB_ROOT}/sistema/descargar-pdf/item/{$fact.comprobanteId}" class="spanAll">
                                    <img src="{$WEB_ROOT}/images/pdf_icon.png"  id="{$fact.comprobanteId}" border="0" title="Descargar PDF"/>
                                </a>
                            {/if}
                        {*/if*}
                        {*descargar xml*}
                        {*if in_array(129,$permissions) || $User.isRoot*}
                            <a href="{$WEB_ROOT}/sistema/descargar-xml/item/{$fact.comprobanteId}" class="spanAll" title="Descargar xml">
                                <img src="{$WEB_ROOT}/images/icons/xml-18.png" border="0" />
                            </a>
                        {*/if*}
                        {if $fact.pagado neq "Pagado" && $fact.status neq '0'}
                            <a href="{$WEB_ROOT}/add-payment/id/{$fact.comprobanteId}" class="spanAll" title="Agregar pago" target="_blank">
                                <img src="{$WEB_ROOT}/images/icons/dolar-box-18.png"/>
                            </a>
                        {/if}
                </td>
              </tr>
             