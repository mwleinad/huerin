<div>
El comprobante ha sido generada exitosamente, ahora puedes proceder a guardar los archivos. No te preocupes, estos archivos los podras volver a ver en "Consultar Comprobantes"
<br /><br />
{if $info.version == "v3" || $info.version == "construc"}
<a href="{$WEB_ROOT}/util/download.php?path={$comprobante.path}&secPath=xml&filename=SIGN_{$comprobante.xml}.xml&contentType=text/xml"><img src="{$WEB_ROOT}/images/xml_icon.png" height="100" width="100" border="0" alt="xml" title="xml"/> </a>
{elseif $info.version == "2"}
<a href="{$WEB_ROOT}/util/download.php?path={$comprobante.path}&secPath=xml&filename={$comprobante.xml}.xml&contentType=text/xml"><img src="{$WEB_ROOT}/images/xml_icon.png" height="100" width="100" border="0" alt="xml" title="xml"/> </a>
{/if}

<a href="{$WEB_ROOT}/util/download.php?path={$comprobante.path}&secPath=pdf&filename={$comprobante.xml}.pdf&contentType=text/pdf"><img src="{$WEB_ROOT}/images/pdf_icon.png" height="100" width="100" border="0"  alt="pdf" title="pdf"/> </a>

<a href="{$WEB_ROOT}/sistema/nueva-factura">Crear nuevo comprobante</a>
</div>