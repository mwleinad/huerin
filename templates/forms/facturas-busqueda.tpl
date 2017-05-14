    <div class="clear"></div>
    <div id="portlets">
    <!--THIS IS A WIDE PORTLET-->
    <div class="portlet">
        <div class="portlet-header fixed">Filtros de Busqueda</div>
		<div class="portlet-content nopadding">
        <form name="frmBusqueda" id="frmBusqueda" method="post" action="">
     		<input type="hidden" name="type" id="type" value="buscar" />
          <table width="100%" cellpadding="0" cellspacing="0" id="box-table-a" summary="Employee Pay Sheet">
            <thead>
              <tr>
                <th width="40" scope="col">Folio de.</th>
                <th width="40" scope="col">Folio a.</th>
                <th width="70" scope="col">RFC</th>
                <th width="90" scope="col">Nombre</th>
                <th width="90" scope="col">Mes</th>
                <th width="30" scope="col">A&ntilde;o</th>
                <th width="90" scope="col">Estatus</th>
                <th width="90" scope="col">Comprobante</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td align="center"><input type="text" size="3" name="folio" id="folio" class="largeInput" /></td>
                <td align="center"><input type="text" size="3" name="folioA" id="folioA" class="largeInput" /></td>
                <td align="center"><input type="text" size="8" name="rfc" id="rfc" class="largeInput" /></td>
                <td align="center"><input type="text" size="10" name="nombre" id="nombre" class="largeInput" /></td>
                <td align="center"><select name="mes" id="mes"  class="largeInput" >
                    <option value="0">Todos</option>
                    {foreach from=$meses item=item key=key}
                        <option value="{$item.id}">{$item.nombre}</option>
                    {/foreach}
                </select></td>
                <td align="center"><input type="text" size="3" name="anio" id="anio"  class="largeInput"  /></td>
                <td align="center"><select name="status_activo" id="status_activo"  class="largeInput" >
                    <option value="">Todos</option>
                    <option value="1">Activos</option>
                    <option value="0">Cancelados</option>
                </select></td>
                <td align="center"><select name="comprobante" id="comprobante"  class="largeInput" >
                    <option value="0">Todos</option>
                    {foreach from=$tipos_comprobantes item=item key=key}
                        <option value="{$item.tiposComprobanteId}">{$item.nombre}</option>
                    {/foreach}
                </select></td>
              </tr>
              <tr>
                <td colspan="8" align="center">Facturador</td>
              </tr>
              <tr>
                <td colspan="8" align="center">
                	<select name="facturador" id="facturador"  class="largeInput" >
                    <option value="">Todos</option>
                    <option value="30">BHSC CONTADORES SC</option>
                    <option value="1">BRAUN HUERIN SA</option>
                    <option value="29">JACOBO BRAUN BRUCKMAN</option>
                </select></td>
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
        <div align="center">
        		<a class="button" name="btnBuscar" id="btnBuscar"><span>Buscar</span></a>
            
            <!--
            <a name="btnExportar" id="btnExportar">Paso 1. Generar Reporte basado en los filtros actuales</a><br />
            <a href="{$WEB_ROOT}/download.php?file=reporte_comprobantes.csv" title="Descargar Ultimo Reporte Generado">
            <img title="Generar Reporte de Comprobantes" src="{$WEB_ROOT}/images/excel.PNG" /></a>
			-->
            <div id="loadBusqueda" style="display:none"><img src="http://www.facturase.com/images/loading.gif" width="16" height="16" />Cargando...</div>       	
        </div>
     </form>
<!-- End Form -->