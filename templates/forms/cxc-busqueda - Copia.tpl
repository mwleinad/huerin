
    <div class="clear"></div>
    <div id="portlets">
    <!--THIS IS A WIDE PORTLET-->
    <div class="portlet">
        <div align="center">Filtros de B&uacute;squeda</div>
		<div class="portlet-content nopadding">
    <div id="divForm">
        <form name="frmBusqueda" id="frmBusqueda" method="post" action="">
     		<input type="hidden" name="type" id="type" value="buscar" />
          <table width="800px" cellpadding="0" cellspacing="0" id="box-table-a" summary="Employee Pay Sheet">
            <thead>
              <tr>
                <th width="" scope="col">Facturador</th>
                <th width="" scope="col">Folio de.</th>
                <th width="" scope="col">Folio a.</th>
                <th width="" scope="col">Cliente o RS</th>
                <th width="" scope="col">Mes</th>
                <th width="" scope="col">A&ntilde;o</th>
                <th width="" scope="col">Estatus</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td align="center"><select name="facturador" id="facturador"  class="largeInput" >
                    <option value="0">Todos</option>
                    <option value="15" {if $sesion.cxc.facturador == "15"}selected{/if}>Braun Huerin SC</option>
                    <option value="20" {if $sesion.cxc.facturador == "20"}selected{/if}>Jacobo Braun</option>
                    <option value="Efectivo" {if $sesion.cxc.facturador == "Efectivo"}selected{/if}>Efectivo</option>
                </select></td>
                <td align="center"><input type="text" size="3" name="folio" id="folio" class="largeInput" value="{$sesion.cxc.folio}"/></td>
                <td align="center"><input type="text" size="3" name="folioA" id="folioA" class="largeInput" value="{$sesion.cxc.folioA}"/></td>
                <td align="center"><input type="text" size="8" name="nombre" id="nombre" class="largeInput" value="{$sesion.cxc.nombre}" autocomplete="off"/>
              <div id="loadingDivDatosFactura"></div>
                                      <div style="position:relative">
                                  <div style="display:none;position:absolute;top:-2px; left:2px; z-index:100" id="suggestionDiv">
                                  </div>
                              </div>
                </td>
                <td align="center"><select name="mes" id="mes"  class="largeInput" >
                    <option value="0">Todos</option>
                    {foreach from=$meses item=item key=key}
                        <option value="{$item.id}" {if $sesion.cxc.mes == $item.id}selected{/if}>{$item.nombre}</option>
                    {/foreach}
                </select></td>
                <td align="center"><input type="text" size="3" name="anio" id="anio"  class="largeInput" value="{$sesion.cxc.anio}" /></td>
                <td align="center"><select name="status_activo" id="status_activo"  class="largeInput" >
                    <option value="todos">Todos</option>
                    <option value="adeuda" {if $sesion.cxc.status_activo == "adeuda"}selected{/if}>Con Adeudo</option>
                    <option value="pagada" {if $sesion.cxc.status_activo == "pagada"}selected{/if}>Pagadas</option>
                </select></td>
              </tr>
            </tbody>
          </table>
        </form>
        </div>
		</div>
      </div>


	<!-- form-->
     <form name="frmBusqueda" id="frmBusqueda" method="post" action="">
     <input type="hidden" name="type" id="type" value="buscar" />
        <div class="folioRowOff" style="width:910px"></div>
        <div style="margin-left:420px">
        	<a class="button" name="btnBuscar" id="btnBuscar"><span>Buscar</span></a>
		</div>
        <div style="clear:both"></div>
        <br />
        <div id="loadBusqueda" style="display:none" align="center">
            <img src="http://www.facturase.com/images/loading.gif" width="16" height="16" />
            <br />
            Cargando...
        </div> 
     </form>
	<!-- End Form -->
