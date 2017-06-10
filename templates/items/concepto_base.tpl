<tr id="conceptoDiv{$key}">
  <td id="conceptoBaseUserId{$key}">{$key}</td>
  <td>{$concepto.cantidad|number_format:2:".":","}</td>
  <td>{$concepto.unidad}</td>
  <td>{$concepto.noIdentificacion}</td>
  <td style="font-family:'Courier New', Courier, monospace; text-align:justify">{$concepto.categoriaConcepto|nl2br}</td>
  <td style="font-family:'Courier New', Courier, monospace; text-align:justify">{$concepto.descripcion|nl2br}</td>
  <td>{$concepto.valorUnitario|number_format:2:".":","}</td>
  <td>{$concepto.importe|number_format:2:".":","}</td>
  <td>{$concepto.excentoIva}</td>
  <td> <span class="linkBorrar" style="cursor:pointer">Borrar</span>
</tr>
