        <div style="clear:both"></div>
        <div id="accionList">
<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a" summary="Employee Pay Sheet">
            <thead>
              <tr>
                <th width="100" scope="col">Ver Factura</th>
                {if $info.version == "v3" || $info.version == "2" || $info.version == "construc"}
                <th width="100" scope="col">Enviar Correo</th>
               	{/if}
                <th width="100" scope="col">Descargar PDF</th>
                {if $info.version == "v3" || $info.version == "2" || $info.version == "construc"}
                <th width="100" scope="col">Descargar XML</th>
               	{/if}
              </tr>
            </thead>
            <tbody> 
                <tr>
                <td width="34">
                <a href="{$WEB_ROOT}/sistema/ver-pdf/item/{$id_comprobante}" target="_blank">
                	<img src="{$WEB_ROOT}/images/icons/ver_factura.png" border="0" />
                </a></td>
                <td> 
                {if $info.version == "v3" || $info.version == "2" || $info.version == "construc"}
                <a href="javascript:void(0)">
                    <img src="{$WEB_ROOT}/images/icons/email.png" border="0" class="spanSend" id="{$id_comprobante}" />
                </a>
               	{/if}
                   </td>
                <td>
                <a href="{$WEB_ROOT}/sistema/descargar-pdf/item/{$id_comprobante}">
                	<img src="{$WEB_ROOT}/images/icons/descargar.png" border="0" />
                </a></td>
                <td>
                {if $info.version == "v3" || $info.version == "2" || $info.version == "construc"}
                <a href="{$WEB_ROOT}/sistema/descargar-xml/item/{$id_comprobante}">
                	<img src="{$WEB_ROOT}/images/icons/descargar.png" border="0" />
                </a>
                	{/if}
               </td>
                </tr>
                </tbody>
                </table>   
</div>