              <tr>
                <td align="center">{$fact.serie}{$fact.folio}</td>
                <td align="center">{if $fact.procedencia eq 'manual'}Factura manual{else}Factura automatica{/if}</td>
                <td align="center">{$fact.fecha|date_format:'%m-%d-%Y'}</td>
                <td align="center">$ {$fact.total|number_format:2}</td>
                <td align="right">{if $fact.status eq '1'}Activa{else}Cancelado{/if}</td>
                <td align="right">${$fact.totalPagos|number_format:2}</td>
                <td align="right">{if $fact.status eq '1'}{$fact.pagado}{else}Cancelado{/if}</td>
                <td width="90">
                        {*descargar xml*}
                        {if in_array(128,$permissions) || $User.isRoot}
                        <a target="_blank" href="{$WEB_ROOT}/cfdi33-generate-pdf&filename=UID_{$fact.comprobanteId}&type=view">
                            <img src="{$WEB_ROOT}/images/pdf_icon.png" height="16" width="16" border="0" title="Ver factura"/>
                        </a>
                        {/if}
                        {*descargar xml*}
                        {if in_array(129,$permissions) || $User.isRoot}
                            <a href="{$WEB_ROOT}/sistema/descargar-xml/item/{$fact.comprobanteId}">
                                <img src="{$WEB_ROOT}/images/icons/descargar.png" border="0" width="16" />
                            </a>
                        {/if}
                </td>
              </tr>
             