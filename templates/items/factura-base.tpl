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
                        {*ver factura*}
                        {if $fact.version == '3.3'}
                            <a target="_blank" href="{$WEB_ROOT}/cfdi33-generate-pdf&filename=SIGN_{$fact.xml}&type=view" title="Ver PDF">
                                <img src="{$WEB_ROOT}/images/icons/ver_factura.png" height="16" width="16" border="0"/>
                            </a>
                        {else}
                            <a href="{$WEB_ROOT}/sistema/ver-pdf/item/{$fact.comprobanteId}" target="_blank" title="Ver PDF">
                                <img src="{$WEB_ROOT}/images/icons/ver_factura.png" border="0" width="16" />
                            </a>
                        {/if}

                        {*enviar correo*}
                        <a href="javascript:void(0)" title="Enviar correo">
                            <img src="{$WEB_ROOT}/images/icons/email.png" border="0" onclick="EnviarEmail({$fact.comprobanteId})" width="16" />
                        </a>

                        {*descargar pdf*}
                        {if $fact.version == '3.3'}
                            <a target="_blank" href="{$WEB_ROOT}/cfdi33-generate-pdf&filename=SIGN_{$fact.xml}&type=download">
                                <img src="{$WEB_ROOT}/images/pdf_icon.png" height="16" width="16" border="0" title="Descargar PDF"/>
                            </a>
                        {else}
                            <a href="{$WEB_ROOT}/sistema/descargar-pdf/item/{$fact.comprobanteId}">
                                <img src="{$WEB_ROOT}/images/pdf_icon.png" class="" id="{$fact.comprobanteId}" border="0" title="Descargar PDF" width="16"/>
                            </a>
                        {/if}


                        {*descargar xml*}
                        <a href="{$WEB_ROOT}/sistema/descargar-xml/item/{$fact.comprobanteId}" title="Descargar XML">
                            <img src="{$WEB_ROOT}/images/icons/descargar.png" border="0" width="16" />
                        </a>
                        {/if}

                        {if $fact.status == 1}
                            <a href="javascript:void(0)">
                                <img src="{$WEB_ROOT}/images/icons/cancel.png" class="spanCancel" id="{$fact.comprobanteId}" border="0" title="Cancelar"/></a>
                        {/if}

{*
    	            <img src="{$WEB_ROOT}/images/icons/details.png" class="spanDetails" id="{$fact.comprobanteId}" border="0" alt="Ver Detalles" /></a>
                    {if $fact.status == 1}
                        <a href="javascript:void(0)"><img src="{$WEB_ROOT}/images/icons/cancel.png" class="spanCancel" id="{$fact.comprobanteId}" border="0" alt="Cancelar"/></a>
                    {/if}
*}
                </td>
              </tr>
             