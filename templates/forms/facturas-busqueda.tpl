    <div class="clear"></div>
    <div id="portlets">
    <!--THIS IS A WIDE PORTLET-->
    <div class="portlet">
        <div class="portlet-header fixed">Filtros de Busqueda</div>
		<div class="portlet-content nopadding">
        <form name="frmBusqueda" id="frmBusqueda" method="post" action="">
     		<input type="hidden" name="type" id="type" value="buscar" />
     		<input type="hidden" name="addComplemento" id="addComplemento" value="1" />
          <table width="100%" cellpadding="0" cellspacing="0" id="box-table-a" summary="Employee Pay Sheet">
            <thead>
              <tr>
                <th style="width: 5%" scope="col">Folio de.</th>
                <th style="width: 5%"scope="col">Folio a.</th>
                <th style="width: 10%" scope="col">RFC</th>
                <th style="width: 20%" scope="col">Nombre</th>
                <th style="width: 18%" scope="col">Encargado</th>
                <th style="width: 4%" scope="col">Subordinados</th>
                <th style="width: 8%"scope="col">Mes</th>
                <th style="width: 4%" scope="col">A&ntilde;o</th>
                <th style="width: 8%" scope="col">Estatus</th>
                <th style="width: 10%" scope="col">Comprobante</th>
                <th style="width: 15%" scope="col">Facturador</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td align="center"><input type="text" size="3" name="folio" id="folio" class="largeInput" value="{$filtros.folio}" /></td>
                <td align="center"><input type="text" size="3" name="folioA" id="folioA" class="largeInput" value="{$filtros.folioA}" /></td>
                <td align="center"><input type="text" size="8" name="rfc" id="rfc" class="largeInput" value="{$filtros.rfc}" /></td>
                <td align="center"><input type="text" size="10" name="nombre" id="nombre" class="largeInput" value="{$filtros.nombre}" /></td>
                <td align="center">
                    <select name="responsableCuenta" id="responsableCuenta"  class="largeInput">
                        {if $User.level eq 1 || $User.allow_visualize_any_contract || $User.allow_any_employee}
                            <option value="0" selected="selected">Todos...</option>
                        {/if}
                        {foreach from=$personals item=personal}
                            <option value="{$personal.personalId}" {if $filtros.responsableCuenta == $personal.personalId} selected="selected" {/if} >{$personal.name}</option>
                        {/foreach}
                    </select>
                </td>
                <td align="center"><input type="checkbox" name="deep" id="deep" {if $filtros.deep}checked{/if}/></td>
                <td align="center"><select name="mes" id="mes"  class="largeInput" >
                    <option value="0">Todos</option>
                    {foreach from=$meses item=item key=key}
                        <option value="{$item.id}" {if $filtros.mes == $item.id} selected {/if}>{$item.nombre}</option>
                    {/foreach}
                </select></td>
                <td align="center"><input type="text" size="3" name="anio" id="anio"  class="largeInput" {if $filtros.anio} value="{$filtros.anio}" {/if}/></td>
                <td align="center"><select name="status_activo" id="status_activo"  class="largeInput" >
                    <option value="">Todos</option>
                    <option value="1" {if $filtros.status_activo == "1"} selected {/if}>Activos</option>
                    <option value="0" {if $filtros.status_activo == "0"} selected {/if}>Cancelados</option>
                </select></td>
                <td align="center"><select name="comprobante" id="comprobante"  class="largeInput" >
                    <option value="0">Todos</option>
                    {foreach from=$tipos_comprobantes item=item key=key}
                        <option value="{$item.tiposComprobanteId}" {if $filtros.comprobante == $item.tiposComprobanteId}} selected {/if}>{$item.nombre}</option>
                    {/foreach}
                </select></td>
                <td align="center">
                    <select name="facturador" id="facturador"  class="largeInput" >
                      <option value="">Todos</option>
                        {foreach from=$emisores item=item key=key}
                            <option value="{$item.rfcId}" {if $filtros.facturador == $item.rfcId}} selected {/if}>{$item.razonSocial}</option>
                        {/foreach}
                    </select>
                </td>
              </tr>
            </tbody>
          </table>
        </form>
		</div>
      </div>


<!-- Form -->
     <form name="frmBusqueda" id="frmBusqueda" method="post" action="">
     <input type="hidden" name="type" id="type" value="buscar" />
        <div class="folioRowOff" style="width:910px">
        </div>
        <div style="text-align: center">
        		<a class="button" id="btnBuscar"><span>Buscar</span></a>
            <!--
            <a name="btnExportar" id="btnExportar">Paso 1. Generar Reporte basado en los filtros actuales</a><br />
            <a href="{$WEB_ROOT}/download.php?file=reporte_comprobantes.csv" title="Descargar Ultimo Reporte Generado">
            <img title="Generar Reporte de Comprobantes" src="{$WEB_ROOT}/images/excel.PNG" /></a>
			-->
            <div id="loadBusqueda" style="display:none"><img src="{$WEB_ROOT}/images/loading.gif" width="16" height="16" />Cargando...</div>
        </div>
     </form>
<!-- End Form -->