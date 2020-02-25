<tr>
    <td>{$folio.razonSocial}</td>
    <td>{$folio.noCertificado}</td>
    <td>{$folio.serie}</td>
    <td>{$folio.folioInicial}</td>
    <td>{$folio.consecutivo}</td>
    <td>{$folio.email}</td>
    <td>{if $folio.logo}<img src="{$folio.logo}" width="50" height="50" />{/if}</td>
    <td>
        <img src="{$WEB_ROOT}/images/icons/edit.gif" class="spanEdit" id="{$folio.serieId}" data-rfc="{$rfcInfo.rfcId}"/>
    </td>

</tr>
