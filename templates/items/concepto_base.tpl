<tr id="conceptoDiv{$key}">
  <td {if $concepto.servicioId > 0}style="background:grey; font-weight: bold" title="Vinculado al servicio de la empresa" {/if}
      id="conceptoBaseUserId{$key}">{$key}</td>
  <td>{$concepto.cantidad|number_format:2:".":","}</td>
  <td>{$concepto.unidad}</td>
  <td>{$concepto.noIdentificacion}</td>
  <td style="font-family:'Courier New', Courier, monospace; text-align:justify">{$concepto.descripcion|nl2br}</td>
  <td>{$concepto.valorUnitario|number_format:2:".":","}</td>
  <td>{$concepto.importe|number_format:2:".":","}</td>
  <td>{$concepto.excentoIva}</td>
  <td>  <span title="Eliminar concepto"
              class="linkBorrar"
              style="cursor:pointer">
          <img src="{$WEB_ROOT}/images/icons/action_delete.gif">
        </span>
        <a title="Editar concepto"
           onclick="CargarConcepto({$key})"
           href="javascript:;">
          <img src="{$WEB_ROOT}/images/icons/edit.gif">
        </a>
</tr>
