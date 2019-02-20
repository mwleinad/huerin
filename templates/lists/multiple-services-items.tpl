<table id="box-table-a" cellspacing="0" cellpadding="0" class="tableCustom100">
    <thead>
      <th>Nombre servicio</th>
      <th>Inicio operaciones</th>
      <th>Inicio factura</th>
      <th>Costo</th>
      <th></th>
    </thead>
    <tbody>
      {foreach from=$itemsServices item=item key=key}
          <tr>
              <td>{$item.nombreServicio}</td>
              <td>{$item.inicioOperaciones|date_format:"%d/%m/%Y"}</td>
              <td>{if $item.inicioFactura!=""}{$item.inicioFactura|date_format:"%d/%m/%Y"}{/if}</td>
              <td>{$item.costo}</td>
              <td>
                  <a href="javascript:;" title="Eliminar servicio" class="spanAll spanDeleteItemService" id="{$key}">
                      <img src="{$WEB_ROOT}/images/icons/delete.png">
                  </a>
              </td>
          </tr>
      {/foreach}
    </tbody>

</table>