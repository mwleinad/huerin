              <tr>
                <td align="center">{if $post.facturador=="Efectivo"}Efectivo{else}{$fact.paymentId}{/if}</td>
                <td align="center">{$fact.metodoDePago}</td>
                <td align="right">{$fact.paymentDate}</td>
                <td align="right">${$fact.amount}</td>
                <td width="90">
                   {if (in_array(126,$permissions) || $User.isRoot)}
                        <img src="{$WEB_ROOT}/images/icons/action_delete.gif" class="spanDeletePayment" id="{$fact.paymentId}" border="0" alt="Borrar Pago" />
                   {/if}
                   {if $fact.ext != '' && (in_array(127,$permissions) || $User.isRoot)}
                    {if $fact.origen eq 'pago'}
                        <a title="Ver Comprobante de Pago" href="{$WEB_ROOT}/download.php?file=payments/{$fact.paymentId}.{$fact.ext}"><img src="{$WEB_ROOT}/images/icons/ver_factura.png" id="{$fact.comprobanteId}" border="0" alt="Ver Factura" width="16" /></a>
                    {else}
                        <a title="Ver Comprobante de Pago" href="{$WEB_ROOT}/download.php?file=payments/{$fact.file}.{$fact.ext}"><img src="{$WEB_ROOT}/images/icons/ver_factura.png" id="{$fact.comprobanteId}" border="0" alt="Ver Factura" width="16" /></a>
                    {/if}
                   {/if}
                   {if $fact.comprobantePagoId}
                        {if in_array(128,$permissions) || $User.isRoot}
                            <a target="_blank" href="{$WEB_ROOT}/cfdi33-generate-pdf&filename=UID_{$fact.comprobantePagoId}&type=download">
                                <img src="{$WEB_ROOT}/images/pdf_icon.png" height="16" width="16" border="0" title="Descargar PDF"/>
                            </a>
                        {/if}
                    {if in_array(129,$permissions) || $User.isRoot}
                        <a href="{$WEB_ROOT}/sistema/descargar-xml/item/{$fact.comprobantePagoId}">
                            <img src="{$WEB_ROOT}/images/icons/descargar.png" border="0" width="16" />
                        </a>
                    {/if}
                    {elseif $fact.origen eq "recuperado"}
                        {if in_array(128,$permissions) || $User.isRoot}
                            <a target="_blank" href="{$WEB_ROOT}/cfdi33-generate-pdf&filename={$fact.nameXmlComplemento}&type=download">
                                <img src="{$WEB_ROOT}/images/pdf_icon.png" height="16" width="16" border="0" title="Descargar PDF"/>
                            </a>
                        {/if}
                    {/if}
                </td>
              </tr>
             