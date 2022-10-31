<div style="text-align: center">

    El comprobante ha sido generada exitosamente, ahora puedes proceder a guardar los archivos.
    <br />Puedes ver estos archivos en consultar comprobantes
    <br />
    <br />

    {*xml*}
    <a href="{$WEB_ROOT}/util/download.php?path={$comprobante.path}&secPath=xml&filename=SIGN_{$comprobante.xml}.xml&contentType=text/xml">
        <img src="{$WEB_ROOT}/images/xml_icon.png" height="100" width="100" border="0" alt="xml" title="xml"/>
    </a>

    {*pdf*}
    {if $comprobante.version|in_array:['3.3','4.0']}
        <a target="_blank" href="{$WEB_ROOT}/cfdi33-generate-pdf&identifier={$comprobante.comprobanteId}&type=download">
            <img src="{$WEB_ROOT}/images/pdf_icon.png" height="100" width="100" border="0" alt="xml" title="xml"/>
        </a>
    {else}
        <a href="{$WEB_ROOT}/util/download.php?path={$comprobante.path}&secPath=pdf&filename={$comprobante.xml}.pdf&contentType=text/pdf" title="Ver Pdf">
            <img src="{$WEB_ROOT}/images/pdf_icon.png" height="100" width="100" border="0" />
        </a>
    {/if}

    <br />
    <br />

    {*email*}
    <a href="javascript:void(0)" onclick="EnviarEmail({$comprobante.comprobanteId})" title="Enviar Comprobante al Cliente">
        <img src="{$WEB_ROOT}/images/icons/email.png" height="50" width="50" border="0" />
    </a>

    <br />
    <br />
    <a href="{$WEB_ROOT}/cfdi33-generate">Crear nuevo comprobante</a>
</div>