<table width="100%" class="font-smaller">
    <tbody>
    <tr>
        <td width="1-0%" valign="top" class="word-break">
            <strong>Total con letra:</strong><br>
            <span class="no-bold">{$xmlData.letra}</span>
        </td>
    </tr>
    <tr>
        <td width="1-0%" valign="top" class="word-break">
            <strong>Sello digital del CFDI:</strong><br>
            <span class="no-bold word-break">{$xmlData.timbreFiscal.SelloCFD|wordwrap:135:"<br />\n":true}</span>
        </td>
    </tr>
    <tr>
        <td width="1-0%" valign="top">
            <strong>Sello digital del SAT:</strong><br>
            <span class="no-bold word-break">{$xmlData.timbreFiscal.SelloSAT|wordwrap:135:"<br />\n":true}</span>
        </td>
    </tr>
    </tbody>
</table>
<table width="100%" class="font-smaller">
    <tbody>
    <tr>
        <td rowspan="2" width="30%" valign="top">
            <img width="200px" src="{$qrFile}">
        </td>
        <td width="70%" valign="top">
            <strong>Cadena Original del complemento de certificación digital del SAT:</strong> <br>
            <span class="no-bold word-break">{$xmlData.timbre.original|wordwrap:100:"<br />\n":true}</span>
        </td>
    </tr>
    <tr>
        <td width="70%" valign="top">
            <table width="100%" class="font-smaller">
                <tbody>
                <tr>
                    <td width="30%" valign="top">
                        <strong>Folio fiscal:</strong> <br>
                    </td>
                    <td width="70%" valign="top" class="no-bold">
                        {$xmlData.timbreFiscal.UUID}
                    </td>
                </tr>
                <tr>
                    <td width="30%" valign="top">
                        <strong>No. de serie del certificado SAT:</strong> <br>
                    </td>
                    <td width="70%" valign="top" class="no-bold">
                        {$xmlData.timbreFiscal.NoCertificadoSAT}
                    </td>
                </tr>
                <tr>
                    <td width="30%" valign="top">
                        <strong>Fecha y hora de certificación:</strong> <br>
                    </td>
                    <td width="70%" valign="top" class="no-bold">
                        {$xmlData.timbreFiscal.FechaTimbrado|replace:'T':' '}
                    </td>
                </tr>
                <tr>
                    <td width="30%" valign="top">
                        <strong>RFC del proveedor de certificación:</strong> <br>
                    </td>
                    <td width="70%" valign="top" class="no-bold">
                        {$xmlData.timbreFiscal.RfcProvCertif}
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>