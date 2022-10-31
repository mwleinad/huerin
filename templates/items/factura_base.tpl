              <tr>
                <td title="Comprobante generado con version {$fact.version}">
                    <small title="Comprobante generado con version {$fact.version}" style="color: darkgreen; font-weight: bold">v{$fact.version}</small></td>
                <td width="34">{$fact.rfc}</td>
                <td>{$fact.nombre}</td>
                <td align="center">{$fact.fecha}</td>
                <td>${$fact.subtotal_formato}</td>
                <td>${$fact.iva_formato}</td>
                <td>${$fact.total_formato}</td>
                <td align="center">{$fact.serie}{$fact.folio}</td>
                <td align="center">
                {if $fact.instanciasLigados|count > 0 || $fact.procedencia eq 'fromInstance'}
                    Factura automatica <br>
                    {foreach from=$fact.instanciasLigados item=ins}
                	    <a href="{$WEB_ROOT}/workflow/id/{$ins.id}" title="Ir a workflow" target="_blank">{$ins.id} - Ir</a><br>
                    {/foreach}
                {elseif $fact.procedencia eq 'fromRifNoInstance'}
                	Factura RIF sin workflow
                {else}
                    Factura manual
                {/if}
                </td>
                <td style="width:100px; word-wrap:break-word;">{$fact.uuid|wordwrap:20:"\n":true}</td>
                <td width="90">{*ver factura*}
                    {if $fact.version|in_array:['3.3','4.0']}
                        {if (in_array(134,$permissions)|| $User.isRoot)}
                        <a target="_blank" href="{$WEB_ROOT}/cfdi33-generate-pdf&identifier={$fact.comprobanteId}&type=view" title="Ver PDF">
                            <img src="{$WEB_ROOT}/images/icons/ver_factura.png" height="16" width="16" border="0"/>
                        </a>
                        {/if}
                    {else}
                        {if (in_array(134,$permissions)|| $User.isRoot)}
                        <a href="{$WEB_ROOT}/sistema/ver-pdf/item/{$fact.comprobanteId}" target="_blank" title="Ver PDF">
                            <img src="{$WEB_ROOT}/images/icons/ver_factura.png" border="0" width="16" />
                        </a>
                        {/if}
                    {/if}

                    {*enviar correo*}
                    {if (in_array(137,$permissions)|| $User.isRoot)}
                        <a href="javascript:void(0)" title="Enviar correo">
                            <img src="{$WEB_ROOT}/images/icons/email.png" border="0" onclick="EnviarEmail({$fact.comprobanteId})" width="16" />
                        </a>
                    {/if}

                    {*descargar pdf*}
                    {if $fact.version|in_array:['3.3','4.0']}
                        {if (in_array(135,$permissions)|| $User.isRoot)}
                        <a target="_blank" href="{$WEB_ROOT}/cfdi33-generate-pdf&identifier={$fact.comprobanteId}&type=download">
                            <img src="{$WEB_ROOT}/images/pdf_icon.png" height="16" width="16" border="0" title="Descargar PDF"/>
                        </a>
                        {/if}
                    {else}
                        {if (in_array(135,$permissions)|| $User.isRoot)}
                        <a href="{$WEB_ROOT}/sistema/descargar-pdf/item/{$fact.comprobanteId}">
                            <img src="{$WEB_ROOT}/images/pdf_icon.png" class="" id="{$fact.comprobanteId}" border="0" title="Descargar PDF" width="16"/>
                        </a>
                       {/if}
                    {/if}


                    {*descargar xml*}
                   {if (in_array(136,$permissions)|| $User.isRoot)}
                    <a href="{$WEB_ROOT}/sistema/descargar-xml/item/{$fact.comprobanteId}" title="Descargar XML">
                        <img src="{$WEB_ROOT}/images/icons/descargar.png" border="0" width="16" />
                    </a>
                    {/if}

                    {if ($fact.status == 1 && !$fact.cfdi_cancel_status )&&(in_array(138,$permissions)|| $User.isRoot)}
                        <a href="javascript:void(0)">
                            <img src="{$WEB_ROOT}/images/icons/cancel.png" class="spanCancel" id="{$fact.comprobanteId}" border="0" title="Cancelar"/></a>
                    {/if}
                </td>
              </tr>
